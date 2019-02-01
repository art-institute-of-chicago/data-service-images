import os
import csv

from func import *

# Directory paths, for ease of use
dir_script = os.path.dirname(os.path.realpath(__file__))
dir_repo = os.path.join(dir_script, '..' )
dir_storage = os.path.join(dir_repo, 'storage', 'app')

dir_data = dir_storage
dir_images = os.path.join(dir_storage, 'images' )

csv_input = os.path.join(dir_data, 'python-input.csv')
csv_output = os.path.join(dir_data, 'python-output.csv')

# Exit early if there is an existing python-output.csv waiting to be digested by PHP
# if os.path.isfile(csv_output):
    # exit()

file_input = open(csv_input, 'r', newline='\n', encoding='utf-8')
file_output = open(csv_output, 'w+', newline='\n', encoding='utf-8')

keys = ['id', 'ahash', 'phash', 'dhash', 'whash']

reader = csv.DictReader(file_input)
writer = csv.DictWriter(file_output, fieldnames=keys)

writer.writeheader()

for row in reader:

    jpg = os.path.join(dir_images, row['id'] + '.jpg')

    if not os.path.isfile(jpg):
        continue

    ahash, phash, dhash, whash = get_image_fingerprint(jpg, row)

    out = {
    	'id': row['id'],
    	'ahash': ahash,
    	'phash': phash,
    	'dhash': dhash,
    	'whash': whash,
    }

    # Output to CSV and console
    writer.writerow(out)
    print(out)

file_input.close()
file_output.close()
