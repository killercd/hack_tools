#!/usr/bin/env python3
import argparse
from pathlib import Path
import sys
import re

def search_content(p: Path, cre: re.Pattern):
    try:
        with p.open('r', errors='ignore') as fh:
            for line in fh:
                if cre.search(line):
                    return True
    except (OSError, UnicodeError):
        return False
    return False


def main():
    ap = argparse.ArgumentParser(description="Find files and weakness")
    ap.add_argument("root", help="Start directory", nargs='?', default='.')
    ap.add_argument("file_pattern", help="File pattern (glob, e.g. '*.py')", nargs='?', default='*')
    ap.add_argument("--pattern","-p", help="Content ()", required=False)
    ap.add_argument("--recursive", "-r", action="store_true", help="Search recursively (default uses rglob; keep for clarity)")
    
    args = ap.parse_args()

    root = Path(args.root)
    if not root.exists():
        print(f"Error: starting directory not found: {root}", file=sys.stderr)
        sys.exit(2)
    if not root.is_dir():
        print(f"Error: {root} is not a directory.", file=sys.stderr)
        sys.exit(2)

    file_pattern = args.file_pattern
    iterator = root.rglob(file_pattern) if args.recursive or True else root.glob(file_pattern)

    for element in iterator:
        if element.is_file():
            if args.pattern:
                content_re = re.compile(args.pattern)
                if search_content(element, content_re):
                    print(element)
            else:
                print(element)

if __name__ == "__main__":
    main()
