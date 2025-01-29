import localforage from "localforage";
import { useState, useEffect } from "react";

export const useCachedImage = (url: string) => {
  const [cachedImage, setCachedImage] = useState<string | null>(null);

  useEffect(() => {
    const fetchAndCacheImage = async () => {
      const cached = await localforage.getItem<string>(url);
      if (cached) {
        setCachedImage(cached);
        return;
      }

      try {
        const response = await fetch(url);
        const blob = await response.blob();

        // Проверка размера
        if (blob.size > 1024 * 1024) {
          console.warn("Image is too large to cache:", url);
          setCachedImage(url);
          return;
        }

        const reader = new FileReader();
        reader.onloadend = () => {
          const base64data = reader.result as string;
          localforage.setItem(url, base64data);
          setCachedImage(base64data);
        };
        reader.readAsDataURL(blob);
      } catch (err) {
        console.error("Error fetching image:", err);
        setCachedImage(null);
      }
    };

    fetchAndCacheImage();
  }, [url]);

  return cachedImage;
};
