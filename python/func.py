import imagehash

from PIL import Image

# https://realpython.com/blog/python/fingerprinting-images-for-near-duplicate-detection/
# https://github.com/JohannesBuchner/imagehash
def get_image_fingerprint(file):

    pil_img = Image.open(file)

    ahash = str(imagehash.average_hash(pil_img))
    phash = str(imagehash.phash(pil_img))
    dhash = str(imagehash.dhash(pil_img))
    whash = str(imagehash.whash(pil_img))

    return ahash, phash, dhash, whash
