<?php

namespace App\Http\ViewComposers;

use App\Models\Common\Media;
use Illuminate\View\View;
use File;
use Image;
use Storage;

class Logo
{

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $logo = '';

        $media_id = setting('company.logo');

        if (setting('invoice.logo')) {
            $media_id = setting('invoice.logo');
        }

        $media = Media::find($media_id);

        if (!empty($media)) {
            $path = Storage::path($media->getDiskPath());

            if (!is_file($path)) {
                return $logo;
            }
        } else {
            $path = asset('public/img/akaunting-logo-green.png');
        }

        $image = Image::make($path)->encode()->getEncoded();

        if (empty($image)) {
            return $logo;
        }

        $extension = File::extension($path);

        $logo = 'data:image/' . $extension . ';base64,' . base64_encode($image);

        $view->with(['logo' => $logo]);
    }
}
