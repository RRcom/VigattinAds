<?php
namespace VigattinAds\Model\Helper;

class Image
{
    private $image_resource;

    /**
     * Convert and resize image to specified width. Height is automatically adjusted based on aspect ratio.
     * This method will return an array if the conversion succeeded.
     * sample return
     * <pre>
     * array(
     *  'width' => 200,
     *  'height' => 300
     * )
     * </pre>
     * @param mixed $source_image_location The image to be converted or resize, can be image location or image from string
     * @param string $new_image_location This will be your new image path and name.
     * @param int $width The width the image will be after the conversion, height is automatically adjusted based on aspect ratio.
     * @param int $quality The desired quality 0 to 100, the higher the good quality but larger file size.
     * @param bool $progressive Either convert image as progressive jpeg. Progressive jpeg first load a low quality image and further on became clearer and clearer until the image download completely.
     * @return array|bool return the new size
     */
    public function base_width_convert_resize($source_image_location, $new_image_location, $width = 320, $quality = 75, $progressive = false)
    {
        if(!$image_source = $this->create_source_from_img($source_image_location)) return false;
        $src_width = imagesx($image_source);
        $src_height = imagesy($image_source);
        if($src_width < $width) $new_size = array('width' => $src_width, 'height' => $src_height );
        else $new_size = array('width' => $width, 'height' => ($src_height/$src_width)*$width );
        $temp_image = imagecreatetruecolor($new_size['width'], $new_size['height']);
        imagecopyresampled($temp_image, $image_source, 0, 0, 0, 0, $new_size['width'], $new_size['height'], $src_width, $src_height);
        imageinterlace($temp_image, $progressive);
        if(!imagejpeg($temp_image, $new_image_location, $quality)) return false;
        return $new_size;
    }

    /**
     * Convert and resize image based on the limit. Either the height or width will be checked if they are above the limit and resize it to the limit specified.
     * This method will return an array if the conversion succeeded.
     * sample return
     * <pre>
     * array(
     *  'width' => 200,
     *  'height' => 300
     * )
     * </pre>
     * @param mixed $source_image_location The image to be converted or resize, can be image location or image from string
     * @param string new_image_location This will be your new image path and name.
     * @param int $size_limit The max size allowed for the image. If the width is over the limit, width will be resize to the limit specified and the height will be automatically adjusted based on aspect ration. If height is above limit, just a vise versa of width will happen.
     * @param int $quality The desired quality 0 to 100, the higher the good quality but larger file size.
     * @param bool $progressive Either convert image as progressive jpeg. Progressive jpeg first load a low quality image and further on became clearer and clearer until the image download completely.
     * @param int $resize_if_smaller_than If the image is to small the image will be resize size specified.
     * @return array|bool
     */
    public function add_convert_image($source_image_location, $new_image_location, $size_limit = 0, $quality = 75, $progressive = false, $resize_if_smaller_than = 500)
    {
        if(!$image_source = $this->create_source_from_img($source_image_location)) return false;
        $src_width = imagesx($image_source);
        $src_height = imagesy($image_source);
        $enlarge_size = $this->enlarge_photo($src_width, $src_height, $resize_if_smaller_than);
        $new_size = $this->size_converter($enlarge_size['width'], $enlarge_size['height'], $size_limit);
        $temp_image = imagecreatetruecolor($new_size['width'], $new_size['height']);
        imagecopyresampled($temp_image, $image_source, 0, 0, 0, 0, $new_size['width'], $new_size['height'], $src_width, $src_height);
        imageinterlace($temp_image, $progressive);
        if(!imagejpeg($temp_image, $new_image_location, $quality)) return false;
        return $new_size;
    }

    /**
     * Resize and crop image base on size specified.
     * This method will return an array if the conversion succeeded.
     * sample return
     * <pre>
     * array(
     *  'width' => 200,
     *  'height' => 300
     * )
     * </pre>
     * @param mixed $source_image_location The image to be crop, can be image location or image from string.
     * @param string $new_image_location This will be your new image path and name.
     * @param int $image_height The output height
     * @param int $image_width The output width
     * @param int $quality The desired quality 0 to 100, the higher the good quality but larger file size.
     * @param bool $progressive Either convert image as progressive jpeg. Progressive jpeg first load a low quality image and further on became clearer and clearer until the image download completely.
     * @return array|bool
     */
    public function add_crop_image($source_image_location, $new_image_location, $image_height = 0, $image_width = 0, $quality = 75, $progressive = false)
    {
        if(!$image_source = $this->create_source_from_img($source_image_location)) return false;
        if(!$image_width || !$image_height)
        {
                $image_width  = imagesx($image_source);
                $image_height = imagesy($image_source);
                $width_new	= imagesx($image_source);
                $height_new	= imagesy($image_source);
                $src_x = 0;
                $src_y = 0;
        }
        else list($width_new, $height_new, $src_x, $src_y) = $this->image_size_zoom($image_width, $image_height, imagesx($image_source), imagesy($image_source));
        $image_thumb = imagecreatetruecolor($image_width, $image_height);
        imagecopyresampled($image_thumb, $image_source, 0, 0, $src_x, $src_y, $image_width, $image_height, $width_new, $height_new);
        imageinterlace($image_thumb, $progressive);
        if(!imagejpeg($image_thumb, $new_image_location, $quality)) return false;
        return array('width' => $image_width, 'height' => $image_height );
    }

    /**
     * Clear temporary image resources.
     */
    public function clear_cache() {
        $this->image_resource = '';
    }

    /**
     * @param $image_file The image to be converted as a resources, can be image location or image from string.
     * @return resource
     */
    public function create_source_from_img($image_file)
    {
        if(is_resource($this->image_resource)) return $this->image_resource;

        // if url
        if(filter_var($image_file, FILTER_VALIDATE_URL)) {
            $this->image_resource = @imagecreatefromstring(@file_get_contents($image_file));
            return $this->image_resource;
        }
        // if file
        else if(is_file($image_file)) {
            $this->image_resource = @imagecreatefromstring(@file_get_contents($image_file));
            return $this->image_resource;
        }
        // if string
        else {
            $this->image_resource = @imagecreatefromstring($image_file);
            return $this->image_resource;
        }
    }

    /**
     * Size enlarger
     * @param int $src_width
     * @param int $src_height
     * @param int $resize_if_lower_than
     * @return array new size
     */
    public function enlarge_photo($src_width, $src_height, $resize_if_lower_than = 0)
    {
        if(!$resize_if_lower_than) return array('width' => $src_width, 'height' => $src_height);
        if($src_width > $src_height)
        {
            if($src_width < $resize_if_lower_than)
            {
                    $new_width = $resize_if_lower_than;
                    $new_height = ($src_height/$src_width)*$new_width;
                    return array('width' => $new_width, 'height' => $new_height);
            }
            return array('width' => $src_width, 'height' => $src_height);
        }
        else 
        {
            if($src_height < $resize_if_lower_than)
            {
                    $new_height = $resize_if_lower_than;
                    $new_width = ($src_width/$src_height)*$new_height;
                    return array('width' => $new_width, 'height' => $new_height);
            }
            return array('width' => $src_width, 'height' => $src_height);
        }
    }

    /**
     * Size converter
     * @param int $src_width
     * @param int $src_height
     * @param int $size_limit
     * @return array
     */
    public function size_converter($src_width, $src_height, $size_limit = 0)
    {
        $new_size = array('width' => $src_width, 'height' => $src_height);
        if(!$size_limit) return $new_size;
        if($src_width > $src_height)
        {
            if($src_width < $size_limit) $new_width = $src_width;
            else $new_width = $size_limit;
            $new_height=($src_height/$src_width)*$new_width;
        }
        else 
        {
            if($src_height < $size_limit) $new_height = $src_height;
            else $new_height = $size_limit;
            $new_width = ($src_width/$src_height)*$new_height;
        }
        $new_size = array('width' => $new_width, 'height' => $new_height);
        return $new_size;
    }

    /**
     * Size zoom generator
     * @param $zoom_width
     * @param $zoom_height
     * @param $image_width
     * @param $image_height
     * @return array
     */
    public function image_size_zoom($zoom_width, $zoom_height, $image_width, $image_height)
    {
        $zoom_width_ratio = $zoom_width/$zoom_height;
        $zoom_height_ratio = $zoom_height/$zoom_width;
        $image_width_ratio = $image_width/$image_height;
        $image_height_ratio = $image_height/$image_width;

        if($zoom_width_ratio > $zoom_height_ratio)
        {
            if($zoom_width_ratio > $image_width_ratio)
            {
                $new_width = $image_width;
                $new_height = $zoom_height_ratio*$image_width;
                $new_x = 0;
                $new_y = ($image_height-$new_height)/(2);
            }
            else
            {
                $new_width = $zoom_width_ratio*$image_height;
                $new_height = $image_height;
                $new_x = ($image_width-$new_width)/(2);
                $new_y = 0;
            }
        }
        else
        {
            if($zoom_height_ratio > $image_height_ratio)
            {
                $new_width = $zoom_width_ratio*$image_height;
                $new_height = $image_height;
                $new_x = ($image_width-$new_width)/(2);
                $new_y = 0;
            }
            else
            {
                $new_width = $image_width;
                $new_height = $zoom_height_ratio*$image_width;
                $new_x = 0;
                $new_y = ($image_height-$new_height)/(2);
            }
        }
        $new_size = array($new_width, $new_height, $new_x, $new_y);
        return $new_size;
    }
}
