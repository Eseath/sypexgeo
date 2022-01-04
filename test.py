import os
import sys
import signal
import subprocess
import yaml
import time
import threading

class bcolors:
    SUCCESS = '\033[92m'
    FAILURE = '\033[91m'
    LOADING = '\033[96m'
    BOLD = '\033[1m'
    END = '\033[0m'

class Spinner:
    text = ''

    def __init__(self, text=''):
        self.stop_running = threading.Event()
        self.spin_thread  = threading.Thread(target=self.initSpin)
        self.text = text
        self.view = self.makeView()

    def makeView(view):
        while True:
            for cursor in ['⠋', '⠙', '⠹', '⠸', '⠼', '⠴', '⠦', '⠧', '⠇', '⠏']:
                yield cursor

    def start(self):
        self.spin_thread.start()

    def stop(self):
        self.stop_running.set()
        self.spin_thread.join()

    def initSpin(self):
        while not self.stop_running.is_set():
            sys.stdout.write(bcolors.LOADING + next(self.view) + bcolors.END + self.text)
            sys.stdout.flush()
            time.sleep(0.1)
            sys.stdout.write('\r')

def hideCursor():
    sys.stdout.write("\033[?25l")
    sys.stdout.flush()

def showCursor():
    sys.stdout.write("\033[?25h")
    sys.stdout.flush()

def handleSignal(sig, frame):
    showCursor()
    sys.exit(0)

def runTask(cmd, description):
    spinner = Spinner(text=f' {description}')
    spinner.start()
    result = subprocess.run(cmd, capture_output=True, text=True)
    spinner.stop()
    if result.returncode == 0:
        sys.stdout.write(bcolors.SUCCESS + '✓' + bcolors.END + f' {description}\n')
    else:
        sys.stdout.write(bcolors.FAILURE + '⨯' + bcolors.END + f' {description}\n')
    sys.stdout.flush()

def runDockerService(serviceName):
    runTask(
        cmd = ['docker', 'compose', 'run', '--rm', serviceName],
        description = f'Testing in the {bcolors.BOLD}{serviceName}{bcolors.END} container'
    )

signal.signal(signal.SIGINT, handleSignal)

with open('docker-compose.yml', 'r') as stream:
    hideCursor()
    runTask(['docker', 'compose', 'build'], 'Building docker containers')
    try:
        services = yaml.safe_load(stream)['services']
        services = filter(lambda service: 'laravel' in service, services)
        for service in services:
            runDockerService(service)
    except yaml.YAMLError as e:
        print(e)
    showCursor()
