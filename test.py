import os
import sys
import signal
import subprocess
import yaml
import time
import threading


class Colors:
    SUCCESS = '\033[92m'
    FAILURE = '\033[91m'
    LOADING = '\033[96m'
    BOLD = '\033[1m'
    END = '\033[0m'


class Spinner:
    text = ''

    def __init__(self, text=''):
        self.stop_running = threading.Event()
        self.spin_thread = threading.Thread(target=self.init_spin)
        self.text = text
        self.view = self.make_view()

    @staticmethod
    def make_view():
        while True:
            for cursor in ['⠋', '⠙', '⠹', '⠸', '⠼', '⠴', '⠦', '⠧', '⠇', '⠏']:
                yield cursor

    def start(self):
        self.spin_thread.start()

    def stop(self):
        self.stop_running.set()
        self.spin_thread.join()

    def init_spin(self):
        while not self.stop_running.is_set():
            sys.stdout.write(Colors.LOADING + next(self.view) + Colors.END + self.text)
            sys.stdout.flush()
            time.sleep(0.1)
            sys.stdout.write('\r')


def hide_cursor():
    sys.stdout.write("\033[?25l")
    sys.stdout.flush()


def show_cursor():
    sys.stdout.write("\033[?25h")
    sys.stdout.flush()


def handle_signal(sig, frame):
    show_cursor()
    sys.exit(0)


def run_task(cmd, description):
    spinner = Spinner(text=f' {description}')
    spinner.start()
    result = subprocess.run(cmd, capture_output=True, text=True)
    spinner.stop()
    if result.returncode == 0:
        sys.stdout.write(Colors.SUCCESS + '✓' + Colors.END + f' {description}\n')
    else:
        sys.stdout.write(Colors.FAILURE + '⨯' + Colors.END + f' {description}\n')
    sys.stdout.flush()


def run_docker_service(service_name):
    run_task(
        cmd=['docker', 'compose', 'run', '--rm', service_name],
        description=f'Testing in the {Colors.BOLD}{service_name}{Colors.END} container'
    )


signal.signal(signal.SIGINT, handle_signal)

with open('docker-compose.yml', 'r') as stream:
    hide_cursor()
    run_task(['docker', 'compose', 'build'], 'Building docker containers')
    try:
        services = yaml.safe_load(stream)['services']
        services = filter(lambda service_name: 'laravel' in service_name, services)
        for service in services:
            run_docker_service(service)
    except yaml.YAMLError as e:
        print(e)
    show_cursor()
