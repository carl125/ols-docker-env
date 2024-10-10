#!/bin/bash

# Path to the directory containing images
IMAGE_DIR="/var/www/vhosts/quanlotkhe.com/html/wp-content/uploads"

# Loop through all .jpeg, .jpg, and .png files in the directory, excluding files with ".bk" in their names
find "$IMAGE_DIR" -type f \( -iname "*.jpeg" -o -iname "*.jpg" -o -iname "*.png" \) | grep -v ".bk" | while read IMAGE; do
    # Get the directory, file name, and file name without the extension
    DIRNAME=$(dirname "$IMAGE")
    BASENAME=$(basename "$IMAGE")
    BASENAME_NO_EXT="${BASENAME%.*}"

    # Check if the file <abc>.webp or <abc>.<old-extension>.webp already exists
    if [ -f "$DIRNAME/$BASENAME_NO_EXT.webp" ] || [ -f "$DIRNAME/$BASENAME.webp" ]; then
        echo "Skip: $BASENAME has already been converted to webp."
        continue
    fi

    # Convert the image to WebP with the appropriate extension in the same directory
    cwebp -q 80 "$IMAGE" -o "$DIRNAME/$BASENAME_NO_EXT.webp"
    echo "Converted: $BASENAME to $BASENAME_NO_EXT.webp in $DIRNAME"
done
