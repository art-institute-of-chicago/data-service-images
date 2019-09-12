import cv2
import urllib
import numpy as np
import matplotlib.pyplot as plt
# %matplotlib inline

def showImage(img):
    plt.axis('off')
    plt.imshow(cv2.cvtColor(img, cv2.COLOR_BGR2RGB))
    plt.waitforbuttonpress()

desiredAspectRatio = 16 / float(9)

img = cv2.imread("1950.1180 - Portrait of Michel Le Tellier, Minister of State.jpg")

gray = cv2.cvtColor(img, cv2.COLOR_RGB2GRAY)
cascade = cv2.CascadeClassifier("./env/Lib/site-packages/cv2/data/haarcascade_frontalface_default.xml")
faceRegions = cascade.detectMultiScale(gray, minNeighbors=7, scaleFactor=1.1)

def drawRegions(source, res, regions, color=(0, 0, 255), size=4):
    for (x, y, w, h) in regions:
        res[y: y + h, x: x + w] = source[y: y + h, x: x + w]
        cv2.rectangle(res, (x, y), (x + w, y + h), color, size)
    return res
faded = (img * 0.65).astype(np.uint8)

interestPoints = cv2.goodFeaturesToTrack(gray, maxCorners=200, qualityLevel=0.01, minDistance=20).reshape(-1, 2)
interestPointRegions = np.concatenate((interestPoints, np.ones(interestPoints.shape)), axis=1).astype(np.int32)

# showImage(drawRegions(img, faded.copy(), interestPointRegions, (255, 255, 255), size=10))
# showImage(drawRegions(img, faded.copy(), faceRegions))

contentRectangles = np.concatenate((faceRegions, interestPointRegions), axis=0)
vis = faded.copy()
# drawRegions(img, vis, interestPointRegions, (255, 255, 255), size=10)
# drawRegions(img, vis, faceRegions)
# showImage(vis)

contentScores = np.multiply(contentRectangles[:, 2], contentRectangles[:, 3])

alpha = img.shape[1] / float(img.shape[0])

if (alpha > desiredAspectRatio):  # the horizontal axis is the principal axis.
    finalWindowLength = int(desiredAspectRatio * img.shape[0])
    projection = np.array([[1, 0, 0, 0], [0, 0, 1, 0]])
else:  # the vertical axis is the principal axis.
    finalWindowLength = int(img.shape[1] / desiredAspectRatio)
    projection = np.array([[0, 1, 0, 0], [0, 0, 0, 1]])
contentRegions = np.dot(projection, contentRectangles.T).T


inflectionPoints = np.concatenate((contentRegions[:, 0], contentRegions[:, 0] + contentRegions[:, 1] - finalWindowLength))
inflectionDeltas = np.concatenate((-contentScores, contentScores))
inflections = np.concatenate((inflectionPoints.reshape(-1, 1), inflectionDeltas.reshape(-1, 1)), axis=1)

inflections = inflections[inflections[:, 0].argsort()]  # Sort by location
inflections = inflections[inflections[:, 0] >= 0]  # drop any outside our range

inflections[:, 1] = np.cumsum(inflections[:, 1])
optimalInflectionPoint = max(enumerate(inflections), key=lambda v: v[1][1])[0]

optimalStartingLocation = (inflections[optimalInflectionPoint, 0] + inflections[optimalInflectionPoint + 1, 0]) / 2

if alpha > desiredAspectRatio:
    optimalCrop = [optimalStartingLocation, 0, finalWindowLength, img.shape[0]]
else:
    optimalCrop = [0, optimalStartingLocation, img.shape[1], finalWindowLength]

result = img[int(optimalCrop[1]): int(optimalCrop[3] + optimalCrop[1]), int(optimalCrop[0]): int(optimalCrop[2] + optimalCrop[0])]
showImage(result)