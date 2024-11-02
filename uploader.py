import subprocess
import sys
import threading
import os

RED = "\033[31m"
GREEN = "\033[32m"
BLUE = "\033[34m"
RESET = "\033[0m"

print(f"{BLUE}===============================")
print("  Sheikh Nightshader's Uploader")
print("===============================\n" + RESET)

if len(sys.argv) < 4:
    print(f"{RED}Usage: python {sys.argv[0]} <URL> <filename> <threads>{RESET}")
    sys.exit(1)

url = sys.argv[1]
filename = sys.argv[2]
threads_count = int(sys.argv[3])

if not os.path.isfile(filename):
    print(f"{RED}File '{filename}' not found!{RESET}")
    sys.exit(1)

def upload_file():
    try:
        command = f'curl -X POST -F "file=@{filename}" {url}'
        response = subprocess.run(command, shell=True, capture_output=True, text=True)
        if response.returncode == 0:
            print(f"{GREEN}File '{filename}' uploaded successfully.{RESET}")
            print(response.stdout)
        else:
            print(f"{RED}Failed to upload '{filename}'. Error: {response.stderr}{RESET}")
    except Exception as e:
        print(f"{RED}Error uploading file: {e}{RESET}")

print(f"{BLUE}Starting upload with {threads_count} threads...{RESET}")
threads = []
for _ in range(threads_count):
    thread = threading.Thread(target=upload_file)
    threads.append(thread)
    thread.start()

for thread in threads:
    thread.join()

print(f"{BLUE}Upload completed.{RESET}")
