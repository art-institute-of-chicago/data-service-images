import imagehash

from PIL import Image

# Helper to parse CSV cells into booleans
# https://stackoverflow.com/questions/31842424/boolean-value-of-fields-in-csv-file-in-python
def get_bool(value):
    return True if value == '1' else False

# https://realpython.com/blog/python/fingerprinting-images-for-near-duplicate-detection/
# https://github.com/JohannesBuchner/imagehash
def get_image_fingerprint(file, row):
    do_ahash = get_bool(row['ahash'])
    do_phash = get_bool(row['phash'])
    do_dhash = get_bool(row['dhash'])
    do_whash = get_bool(row['whash'])

    if not do_ahash and not do_phash and not do_dhash and not do_whash:
        return None, None, None, None

    pil_img = Image.open(file)

    # OSError: image file is truncated (0 bytes not processed)
    try:
        pil_img.load()
    except (IOError, OSError):
        pil_img.close()
        return None, None, None, None

    ahash = str(imagehash.average_hash(pil_img)) if do_ahash else None
    phash = str(imagehash.phash(pil_img)) if do_phash else None
    dhash = str(imagehash.dhash(pil_img)) if do_dhash else None
    whash = str(imagehash.whash(pil_img)) if do_whash else None

    pil_img.close()

    return ahash, phash, dhash, whash
