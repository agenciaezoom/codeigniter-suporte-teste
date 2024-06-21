<?php

require_once(APPPATH.'libraries/WideImage.php');

class Image{

    private $extensions;
    private $error_image;
    private $cache_dir;
    private $png_comp_level;
    private $whideimage;

    public function __construct()
    {
        $this->extensions = array('jpg', 'png', 'jpeg', 'gif', 'bmp', 'JPG', 'PNG', 'JPEG', 'GIF', 'BMP');
        $this->error_image = 'error.jpg';
        $this->cache_dir =  trim(APPROOT . 'userfiles' . DS . 'cache' . DS . 'wideimage' . DS, DS) . DS;

        $this->png_comp_level = 6;

        $this->wideimage = new WideImage();

        //Cria o diretório de cache caso não exista
        if(!is_dir($this->cache_dir))
            $this->createDir($this->cache_dir);
    }

    /**
    * Redimensionamento
    * exemplo: http://server/CI/image/resize?src=temp/imagem.jpg&w=800&h=800&q=75&fit=outside
    *
    * @param string $src caminho da imagem
    * @param integer|% $w largura, default: 100
    * @param integer|% $h altura, default: 100
    * @param % $q qualidade, default: 100
    * @param 'inside'|'outside'|'fill'  $fit  ajuste,  default: inside
    */
    public function resize($params)
    {
        $options = array(
            'src' => '',
            'w' => '100',
            'h' => '100',
            'q' => '100',
            'force' => '0',
            'fit' => 'inside'
        );
        $params = array_merge($options, $params);

        extract($params);

        $file = htmlentities(urldecode($src));

        $file_path = $file;
        if (!preg_match("/^http/i", $file)) {
            $file_path = APPROOT.$file_path;
        }

        if(!is_file($file_path) && !preg_match("/^http/i", $src))
        {
            $ext = pathinfo($this->error_image, PATHINFO_EXTENSION);
            $this->wideimage
                ->load($this->error_image)
                ->resize($w, $h, $fit)
                ->output($ext, $q);
            return;
        }

        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $file_name = md5("resize{$src}{$w}{$h}{$fit}{$q}") . '.' . $ext;

        //allowed extensions
        if (!preg_match("/^" . implode("|", $this->extensions) . "$/", $ext))
        {
            show_404('image/resize/'.$file);
        }

        if( !is_file($this->cache_dir . "{$file_name}") || (!preg_match("/^http/i", $file) && filemtime($this->cache_dir . "{$file_name}") < filemtime($file_path))) {
            $q = $ext=='png'?$this->png_comp_level: $q;

            $img = $this->wideimage
                ->load($file_path);

            if ($force == '1'){
                $img->resize($w, $h, $fit)
                    ->saveToFile($this->cache_dir . "{$file_name}", $q);
            }else{
                $img->resizeDown($w, $h, $fit)
                    ->saveToFile($this->cache_dir . "{$file_name}", $q);
            }

        }
        $this->output($file_name);
    }

    public function resize_canvas($params)
    {
        $options = array(
            'src' => '',
            'w' => '100',
            'h' => '100',
            'q' => '100',
            'fit' => 'inside',
            'cw' => isset($params['w']) ? $params['w'] : '100',
            'ch' => isset($params['h']) ? $params['h'] : '100',
            't' => 'center',
            'l' => 'center',
            'bg' => '0'
        );
        $params = array_merge($options, $params);

        extract($params);

        $file = htmlentities(urldecode($src));

        $file_path = $file;
        if (!preg_match("/^http/i", $file)) {
            $file_path = APPROOT.$file_path;
        }

        if(!is_file($file_path) && !preg_match("/^http/i", $src))
        {
            $ext = pathinfo($this->error_image, PATHINFO_EXTENSION);
            $this->wideimage
                ->load($this->error_image)
                ->resize($w, $h, $fit)
                ->crop($l, $t, $cw, $ch)
                ->output($ext, $q);
            return;
        }

        $ext = $bg ? pathinfo($file, PATHINFO_EXTENSION) : 'png';
        $file_name = md5("canvas{$src}{$w}{$h}{$fit}{$cw}{$ch}{$l}{$t}{$q}{$bg}") . '.' . $ext;

        //allowed extensions
        if (!preg_match("/^" . implode("|", $this->extensions) . "$/", $ext))
        {
            show_404('image/crop/'.$file);
        }

        if(!is_file($this->cache_dir . "{$file_name}") || (!preg_match("/^http/i", $file) && filemtime($this->cache_dir . "{$file_name}") < filemtime($file_path)))
        {
            $q = $ext=='png'?$this->png_comp_level: $q;

            if ($ext=='png'){
                $newImg = WideImage::createTrueColorImage($w, $h);

                $bg = $newImg->getTransparentColor();
                if($bg == -1) {
                    $newImg->saveAlpha(true);
                    $bg = $newImg->allocateColorAlpha(0, 0, 0, 127);
                }

                $newImg->fill(0,0, $bg);
                $resized = $this->wideimage
                    ->load($file_path)
                    ->resizeDown($w, $h, $fit);
                $newImg->merge($resized,'center','center')->saveToFile($this->cache_dir . "{$file_name}", $q);
            }else{
                $this->wideimage
                    ->load($file_path)
                    ->resize($w, $h, $fit)
                    ->resizeCanvas($w, $h,  'center', 'center',  $this->wideimage->load($file_path)->allocateColor(255, 255, 255))
                    ->saveToFile($this->cache_dir . "{$file_name}", $q);
            }
        }

        $this->output($file_name);
    }

    /**
    * Mesclar
    * exemplo: http://192.168.1.190/revista_requinte/image/merge?src=temp/thumb_eventos.jpg&wtm=temp/banner.jpg
    *
    * @param string $src caminho da imagem
    * @param string $wtm caminho para a imagem que irá sobrepor
    * @param integer|% $w largura, default: 100
    * @param integer|% $h altura, default: 100
    * @param integer|% $t ditância de cima para baixo
    * @param integer|% $l ditância da esquerda para direita
    * @param integer $pct transparência da $wtm
    * @param integer $q qualidade da imagem
    */
    public function merge($params)
    {
        $options = array(
            'src' => '',
            'wtm' => '',
            'w' => '100',
            'h' => '100',
            'q' => '100',
            'pct' => '100',
            't' => 'center',
            'l' => 'center'
        );
        $params = array_merge($options, $params);

        extract($params);


        $file = htmlentities(urldecode($src));
        $file_wtm = htmlentities(urldecode($wtm));

        $file_path = $file;
        if (!preg_match("/^http/i", $file)) {
            $file_path = APPROOT.$file_path;
        }

        if(!is_file($file_path) || !is_file($file_wtm))
        {
            $ext = pathinfo($this->error_image, PATHINFO_EXTENSION);
            $this->wideimage
                ->load($this->error_image)
                ->resize($w, $h)
                ->output($ext, $q);
        }

        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $file_name = md5("merge{$src}{$wtm}{$w}{$h}{$t}{$l}{$pct}{$q}") . '.' . $ext;

        //allowed extensions
        if (!preg_match("/^" . implode("|", $this->extensions) . "$/", $ext))
        {
            show_404('image/resize/'.$file);
        }

        if(!is_file($this->cache_dir . "{$file_name}") || (!preg_match("/^http/i", $file) && filemtime($this->cache_dir . "{$file_name}") < filemtime($file_path)))
        {
            $q = $ext=='png'?$this->png_comp_level: $q;
                     $watermark = $this->wideimage->load($file_wtm)->resize($w, $h);
            $this->wideimage
                ->load($file_path)
                ->resize($w, $h, 'outside') //'inside'|'outside'|'fill'
                ->crop('center', 'center', $w, $h)
                ->merge($watermark, $l, $t, $pct)
                ->saveToFile($this->cache_dir . "{$file_name}", $q);
        }
        $this->output($file_name);
    }

    /**
    * Mesclar
    * exemplo: http://localhost/moneo/image/apply_mask?src=application/modules/home/assets/css/img/bg.jpg&mask=temp/mask.png&w=150
    *
    * @param string $src caminho da imagem
    * @param string $mask caminho para a imagem que irá sobrepor
    * @param integer|% $w largura, default: 100
    * @param integer|% $h altura, default: 100
    * @param integer|% $top ditância de cima para baixo
    * @param integer|% $left ditância da esquerda para direita
    * @param integer $pct transparência da $wtm
    * @param integer $q qualidade da imagem
    */
    public function apply_mask($params)
    {
        $options = array(
            'src' => '',
            'mask' => '',
            'w' => '100',
            'h' => '100',
            'pct' => '100',
            'q' => '100',
            't' => 'center',
            'l' => 'center'
        );
        $params = array_merge($options, $params);

        extract($params);

        $file = htmlentities(urldecode($src));
        $file_mask = htmlentities(urldecode($mask));

        $file_path = $file;
        if (!preg_match("/^http/i", $file)) {
            $file_path = APPROOT.$file_path;
        }

        if((!is_file($file_path) && !preg_match("/^http/i", $src)) || (!is_file($file_mask) && !preg_match("/^http/i", $mask)))
        {
            $ext = pathinfo($this->error_image, PATHINFO_EXTENSION);
            $this->wideimage
                ->load($this->error_image)
                ->resize($w, $h)
                ->output($ext, $q);
        }

        $ext = 'png' /*pathinfo($file, PATHINFO_EXTENSION)*/;
        $file_name = md5("apply_mask{$src}{$mask}{$w}{$h}{$t}{$l}{$pct}{$q}") . '.' . $ext;

        //allowed extensions
        if (!preg_match("/^" . implode("|", $this->extensions) . "$/", $ext))
        {
            show_404('image/resize/'.$file);
        }

        if(!is_file($this->cache_dir . "{$file_name}") || (!preg_match("/^http/i", $file) && filemtime($this->cache_dir . "{$file_name}") < filemtime($file_path)))
        {
            $q = $ext=='png'?$this->png_comp_level: $q;
            $watermark = $this->wideimage->load($file_mask)->resize($w, $h);

            $this->wideimage
                ->load($file_path)
                ->resize($w, $h, 'outside') //'inside'|'outside'|'fill'
                ->crop('center', 'center', $w, $h)
                ->applyMask($watermark, $l, $t, $pct)
                //->output('png')
                ->saveToFile($this->cache_dir . "{$file_name}", $q);

        }
        $this->output($file_name);
    }

    /**
    * cropa imagem
    * exemplo: http://server/CI/image/crop?src=temp/imagem.jpg&w=800&h=800&q=75&l=left&t=top
    *
    * @param string $src caminho da imagem
    * @param integer|% $w largura, default: 100
    * @param integer|% $h altura, default: 100
    * @param % $q qualidade, default: 100
    * @param integer|'center'|'right'|'left'|'bottom'|'top'|'middle' $l coordenadas esquerda,  default: center
    * @param integer|'center'|'right'|'left'|'bottom'|'top'|'middle' $t coordenadas topo,  default: center
    */
    public function crop($params)
    {
        $options = array(
            'src' => '',
            'w' => '100',
            'h' => '100',
            'q' => '100',
            't' => 'center',
            'l' => 'center'
        );
        $params = array_merge($options, $params);

        extract($params);

        $file = htmlentities(urldecode($src));

        $file_path = $file;
        if (!preg_match("/^http/i", $file)) {
            $file_path = APPROOT.$file_path;
        }

        if(!is_file($file_path) && !preg_match("/^http/i", $src))
        {
            $ext = pathinfo($this->error_image, PATHINFO_EXTENSION);
            $this->wideimage
                ->load($this->error_image)
                ->crop($l, $t, $w, $h)
                ->output($ext, $q);
            return;
        }

        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $file_name = md5("crop{$src}{$w}{$h}{$l}{$t}{$q}") . '.' . $ext;

        //allowed extensions
        if (!preg_match("/^" . implode("|", $this->extensions) . "$/", $ext))
        {
            show_404('image/crop/'.$file);
        }

        if(!is_file($this->cache_dir . "{$file_name}") || (!preg_match("/^http/i", $file) && filemtime($this->cache_dir . "{$file_name}") < filemtime($file_path)))
        {
            $q = $ext=='png'?$this->png_comp_level: $q;
            $this->wideimage
                ->load($file_path)
                ->crop($l, $t, $w, $h)
                ->saveToFile($this->cache_dir . "{$file_name}", $q);
        }
        $this->output($file_name);
    }

    /**
    * redimensiona e cropa uma imagem
    * exemplo: http://server/CI/image/resize_crop?src=temp/imagem.jpg&w=800&h=800&q=75&l=center&t=center&cw=500&ch=500
    *
    * @param string $src caminho da imagem
    * @param integer|% $w largura, default: 100
    * @param integer|% $h altura, default: 100
    * @param 'inside'|'outside'|'fill'  $fit  ajuste,  default: inside
    * @param % $q qualidade, default: 100
    * @param integer|% $cw largura do crop, default: $w
    * @param integer|% $ch altura do crop, default: $c
    * @param integer|'center'|'right'|'left'|'bottom'|'top'|'middle' $l coordenadas esquerda,  default: center
    * @param integer|'center'|'right'|'left'|'bottom'|'top'|'middle' $t coordenadas topo,  default: center
    */
    public function resize_crop($params)
    {
        $options = array(
            'src' => '',
            'w' => '100',
            'h' => '100',
            'q' => '100',
            'fit' => 'outside',
            'cw' => isset($params['w']) ? $params['w'] : '100',
            'ch' => isset($params['h']) ? $params['h'] : '100',
            't' => 'center',
            'l' => 'center',
            'output' => '1'
        );
        $params = array_merge($options, $params);

        extract($params);

        $file = htmlentities(urldecode($src));

        $file_path = $file;
        if (!preg_match("/^http/i", $src)) {
            $file_path = APPROOT.$file_path;
        }

        if(!is_file($file_path) && !preg_match("/^http/i", $src))
        {
            $ext = pathinfo($this->error_image, PATHINFO_EXTENSION);
            $this->wideimage
                ->load($this->error_image)
                ->resize($w, $h, $fit)
                ->crop($l, $t, $cw, $ch)
                ->output($ext, $q);
        }

        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $file_name = md5("resize_crop{$src}{$w}{$h}{$fit}{$cw}{$ch}{$l}{$t}{$q}") . '.' . $ext;

        //allowed extensions
        if (!preg_match("/^" . implode("|", $this->extensions) . "$/", $ext))
        {
            show_404('image/crop/'.$file);
        }

        if(!is_file($this->cache_dir . "{$file_name}"))
        {
            $q = $ext=='png'?$this->png_comp_level: $q;
            $this->wideimage
                ->load($file_path)
                ->resize($w, $h, $fit)
                ->crop($l, $t, $cw, $ch)
                ->saveToFile($this->cache_dir . "{$file_name}", $q);
        }

        if ($output == '1') {
            $this->output($file_name);
        } else {
            return $file_name;
        }
    }

    /**
    * Remove todas as imagens do cache
    */
    public function clear()
    {
        $scanned_directory = array_diff(scandir($this->cache_dir), array('..', '.'));
        foreach ($scanned_directory as $dir) {
            $this->rrmdir($this->cache_dir . "{$dir}");
        }
    }

    /**
    * Exibe a imagem
    *
    */
    private function output($file_name)
    {
        $ext = pathinfo($this->cache_dir . "{$file_name}", PATHINFO_EXTENSION);
        header('Pragma: public');
        header('Cache-Control: max-age=86400');
        header('Expires: '. gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
        header('Content-Type: image/'.$ext);
        header('Content-Disposition: inline; filename="'.$file_name.'";');
        readfile($this->cache_dir . "{$file_name}");
    }
    /**
    * Remove um diretorio e os arquivos contodos nele
    *
    * @param string $dir caminho do diretorio
    */
    private function rrmdir($dir) {
        if (is_dir($dir))
        {
            $objects = scandir($dir);
            foreach ($objects as $object)
            {
                if ($object != "." && $object != "..")
                {
                    if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    /**
     * Cria o diretório
     * @param  [string] $sourceDirPath caminho a ser criado
     * @return boolean
     */
    private function createDir($sourceDirPath) {
        $res = false;
        if(mkdir($sourceDirPath, 0777, true)) {
            $this->addGitIgnore($sourceDirPath);
            $res = true;
        }

        return $res;
    }

    /**
     * Adiciona o arquivo .gitignore
     */
    public function addGitIgnore($sourceDirPath) {
        //Adiciona o gitignore
        return file_put_contents($sourceDirPath.'/.gitignore', utf8_encode("*\r\n!.gitignore"));
    }
}
