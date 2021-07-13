<?php


namespace app\util;


use yii\helpers\Url;

trait HtmlUtil
{

	public static function getStatoHtml($stato,$testoSi,$testoNo){
		if($stato == 1){
			return "<span class=\"label label-lg label-success label-inline\">$testoSi</span>";
		}

		return "<span class=\"label label-lg label-danger label-inline\">$testoNo</span>";
	}

	public static function generateUserIcon($name = 'name')
	{
	    $integer = rand(0,6);
        $states = [
            'light',
            'success',
            'danger',
            'warning',
            'dark',
            'primary',
            'info'
        ];
		$textStyle = null;
		if($states[$integer] == 'light'){

			$textStyle = "style='color:black'";
		}

		return '
			<div class="d-flex align-items-center">
				<div class="symbol symbol-50 symbol-light-'. $states[$integer] .' flex-shrink-0">
					<div '.$textStyle.' class="symbol-label font-size-h5"> '. substr($name,0,1) .' </div>
				</div>
			</div>
		';
	}

	public static function getActionsList($updateUrl,$deleteUrl,$idPjax = "",$pjaxUpdate = false,$optionalParameters = [], $viewUrl = null){

		$return = "";

		$deleteOptional = $optionalParameters['delete']??null;
		$updateOptional = $optionalParameters['update']??null;

		$updateOptionalString = '';
		$deleteOptionalString = '';

		if(!empty($updateOptional)){
			foreach ($updateOptional as $index => $item) {
				$updateOptionalString .= " $index='$item' ";
			}
		}

		if(!empty($deleteOptional)){
			foreach ($deleteOptional as $index => $item) {
				$deleteOptionalString .= " $index='$item' ";
			}
		}

        if(!empty($viewUrl)){
            $return .= "<a href='" . $viewUrl . "' class='btn btn-sm btn-clean btn-icon btn-update' title='Dettaglio' data-pjax='".($pjaxUpdate ? "1" : "0")."'>
                <i class='la la-eye la-2x'></i>
            </a>";
        }

		if(!empty($updateUrl)) {
			$return .= "<a href='" . $updateUrl . "' ". $updateOptionalString ." class='btn btn-sm btn-clean btn-icon btn-update' title='Modifica' data-pjax='".($pjaxUpdate ? "1" : "0")."'>
            <i class='la la-edit la-2x'></i>
            </a>";
		}

		if(!empty($deleteUrl)) {
			$return .= "<a href='javascript:void(0)' ". $deleteOptionalString." data-pjax='0' data-id-pjax='". $idPjax ."' data-url='".$deleteUrl."' class='btn btn-sm btn-clean btn-delete btn-icon' title='Elimina'>
                <i class='la la-trash la-2x'></i>
            </a>";
		}

		return $return;
	}

	public static function generateRandomColor()
	{
		return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
	}
}