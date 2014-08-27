/**
 * Use to debug code
 * @param message output message shown in console
 */
function log(message) {
    if(enableDebugLog) console.log(message);
}

/**
 * Convert and resize image to base64 string
 * @param img
 * @returns {*}
 */
function getBase64Image(img) {
    var canvas = document.createElement("canvas");
    var newSize = sizeConverter(img.width, img.height, 320)
    canvas.width = newSize.width;
    canvas.height = newSize.height;

    var ctx = canvas.getContext("2d");
    ctx.drawImage(img, 0, 0, img.width, img.height, 0, 0, newSize.width, newSize.height);

    return canvas.toDataURL();
}

/**
 * Size converter
 * @param int $src_width
 * @param int $src_height
 * @param int $size_limit
 * @return array
 */
function sizeConverter(src_width, src_height, size_limit) {
    var new_size = {"width":src_width, "height":src_height};
    var new_width = 0;
    var new_height = 0;
    if(!size_limit) return new_size;
    if(src_width > src_height)
    {
        if(src_width < size_limit) new_width = src_width;
        else new_width = size_limit;
        new_height = (src_height/src_width)*new_width;
    }
    else
    {
        if(src_height < size_limit) new_height = src_height;
        else new_height = size_limit;
        new_width = (src_width/src_height)*new_height;
    }
    new_size = {"width":new_width, "height":new_height};
    return new_size;
}

/* Add comma to a number */
function addCommas(nStr) {
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}

/** Remove comma from number **/
function removeCommas(nStr) {
    nStr = nStr.replace(/\,/g,'');
    console.log(nStr);
    return parseFloat(nStr);
}
