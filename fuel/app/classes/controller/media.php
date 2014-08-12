<?php
/**
 * Materia
 * License outlined in licenses folder
 */

class Controller_Media extends Controller
{

	public function action_show_asset($asset_id)
	{
		// Validate Logged in
		if (Materia\Api::session_valid() !== true ) throw new HttpNotFoundException;

		$asset = Materia\Widget_Asset_Manager::get_asset($asset_id);

		// Validate Asset exists
		if ( ! ($asset instanceof Materia\Widget_Asset)) throw new HttpNotFoundException;

		$file = Config::get('materia.dirs.media').$asset->id.'.'.$asset->type;

		// Validate file exists
		if ( ! file_exists($file)) throw new HttpNotFoundException;

		File::render($file);

		return '';
	}

	public function action_show_large($asset_id)
	{
		$this->_show_resized($asset_id, 'large', 600);
		return '';
	}

	public function action_show_thumnail($asset_id)
	{
		$this->_show_resized($asset_id, 'thumbnail', 75, true);
		return '';
	}

	protected function _show_resized($asset_id, $size_name, $width, $crop=false)
	{
		// Validate Logged in
		if (Materia\Api::session_valid() !== true ) throw new HttpNotFoundException;

		$asset = Materia\Widget_Asset_Manager::get_asset($asset_id);

		// Validate Asset exists
		if ( ! ($asset instanceof Materia\Widget_Asset)) throw new HttpNotFoundException;

		$resized_file = Config::get('materia.dirs.media').$size_name.'/'.$asset->id.'.'.$asset->type;
		// Validate file exists
		if ( ! file_exists($resized_file))
		{
			// thumb doesn't exist, build one if the original file exists
			$orig_file = Config::get('materia.dirs.media').$asset->id.'.'.$asset->type;
			if ( ! file_exists($orig_file)) throw new HttpNotFoundException;

			try
			{
				if ($crop)
				{
					Image::load($orig_file)
						->crop_resize($width, $width)
						->save($resized_file);
				}
				else
				{
					Image::load($orig_file)
						->resize($width, $width * (2 / 3))
						->save($resized_file);
				}
			}
			catch (\RuntimeException $e)
			{
				// use a default image instead
				$resized_file = Config::get('materia.dirs.media').$size_name.'/'.$asset->id.'.jpg';
				if ( ! file_exists($resized_file))
				{
					Image::load(Config::get('materia.no_media_preview'))
						->resize($width, $width)
						->save($resized_file);
				}
			}
		}

		return File::render($resized_file, null, null, 'media');
	}


	public function action_import()
	{
		// Validate Logged in
		if (Materia\Api::session_valid() !== true ) throw new HttpNotFoundException;

		Package::load('casset');
		Casset::enable_js(['media_catalog']);
		Casset::enable_css(['media_catalog']);

		$this->theme = Theme::instance();
		$this->theme->set_template('layouts/main');

		$this->theme->get_template()
			->set('title', 'Media Catalog')
			->set('page_type', 'import');

		$this->theme->set_partial('content', 'partials/catalog/media');

		Casset::js_inline('var BASE_URL = "'.Uri::base().'";');
		return Response::forge(Theme::instance()->render());
	}

	// Handles the upload using plupload's classes
	public function action_upload()
	{
		// Validate Logged in
		if (Materia\Api::session_valid() !== true ) throw new HttpNotFoundException;

		Event::register('media-upload-complete', '\Controller_Media::on_upload_complete');

		Package::load('plupload');
		return \Plupload\Plupload::upload();
	}

	// Event handler called when an upload via plupload is complete
	public static function on_upload_complete($uploaded_file)
	{
		Materia\Widget_Asset_Manager::process_upload(Input::post('name', 'New Asset'), $uploaded_file);
	}

}