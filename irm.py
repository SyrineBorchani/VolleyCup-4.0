import cv2
import numpy as np
import matplotlib.pyplot as plt

img = cv2.imread("hi.jpg", cv2.IMREAD_GRAYSCALE)



plt.imshow(img, cmap='gray')
plt.title('Image Originale')
plt.axis('off')
plt.show()
 
plt.hist(img.ravel(), bins=256, range=(0,255), color='blue')
plt.title('Histogramme Cerveau ')
plt.show()