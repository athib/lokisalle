<?php

/*
 * Classe comportant des méthodes pour générer différents éléments HTML (label, input, table...)
 */
class Builder
{
	public function generateFormInput($data, $controller, $type, $name, $placeholder, $labelClass = '')
	{
		if(strpos($name, 'password') !== false)
			$errorName = 'password';
		else
			$errorName = $name;

		$content = '<div class="form-group has-feedback ';

		if($data)
			$content .= $controller->hasError($errorName) ? 'has-error' : 'has-success';

		$content .= '">';

		$content .= '<label for="' . $name . '" class="' . $labelClass . '">' . $placeholder . '</label>';

		$plcholder_attr = $type !== 'photo' ? 'placeholder="' . $placeholder . '"' : '';
		$content .= '<input type="' . $type . '" id="' . $name . '" name="' . $name . '" class="form-control" ' . $plcholder_attr;

		if($data)
		{
			if($controller->hasError($errorName))
			{
				$content .= ' />';
				$content .= '<span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>';
			}
			else
			{
				$content .= ' value="'. $data[$name] .'" />';
				$content .= '<span class="glyphicon glyphicon-ok form-control-feedback" aria-hidden="true"></span>';
			}
		}
		else
		{
			$content .= ' />';
		}

		$content .= '</div>';


		return $content;
	}

	public function generateFormRadio($name, $text, $data, $checked)
	{
		$radio = '<div class="form-group">';
		$radio .= '<label>' . $text . '</label>';

		foreach($data as $key => $value)
		{
			$ckeckedOrNot = $checked == $key ? 'checked' : '';

			$radio .= '<label class="radio-inline">';
			$radio .= '<input type="radio" name="' . $name . '" value="' . $key . '" ' . $ckeckedOrNot . ' >';
			$radio .= $value  . '</label>';
		}

        $radio .= '</div>';


		return $radio;
	}

	public function generateFormLabel($text, $value)
	{
		$label = '<p>';
		$label .= '<label class="col-md-4">' . $text . '</label>';
		$label .= $value;
		$label .= '</p>';

		return $label;
	}

	public function generateFormSelect($name, $placeholder, $options, $optionToCheck, $labelClass = '')
	{
		$select = '<div class="form-group">';
		$select .= '<label for="' . $name . '" class="' . $labelClass . '">' . $placeholder . '</label>';
		$select .= '<select class="form-control" id="' . $name . '" name="' . $name . '">';

		foreach($options as $key => $value)
		{
			if($value === $optionToCheck || $key === $optionToCheck)
				$selected = 'selected="selected"';
			else
				$selected = '';



			$select .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
		}

		$select .= '</select>';
		$select .= '</div>';

		return $select;
	}

	public function generateFormButton($name, $value, $size_up = false)
	{
		$class_btn = 'btn btn-primary';

		if($size_up)
			$class_btn .= ' btn-lg';

		$button = '<button type="submit" class="' . $class_btn . '" name="' . $name . '">' . $value . '</button>';

		return $button;
	}


	public function generateTable($title, $entetes, $donnees, $edit, $delete, $text_limit, $sort_icons = false, $sort_prefix = '')
	{
		if($edit) $entetes[] = 'Modifier';
		if($delete) $entetes[] = 'Supprimer';

		$table = '<div class="table-responsive">';
		$table .= '<table class="table table-condensed table-hover">';

		$table .= '<thead>';
		$table .= '<tr><th colspan="' . count($entetes) . '" class="text-center">' . $title . '</th></tr>';
		$table .= '<tr>';

		foreach($entetes as $key => $value)
		{
			$table .= '<th>';

			if($sort_icons && $value != 'Modifier' && $value != 'Supprimer' && $value != 'Photo')
			{
				$table .= '<a href="?' . $sort_prefix . 'orderby=' . $key . '&sort=asc">';
				//$table .= '<img class="sort-icon" src="' . RACINE_SITE . 'images/arrow_up.png" alt="Tri croissant"/>';
				$table .= '<span class="glyphicon glyphicon-chevron-up aria-hidden="true">';
				$table .= '</a>';
			}

			$table .= $value;

			if($sort_icons && $value != 'Modifier' && $value != 'Supprimer')
			{
				$table .= '<a href="?' . $sort_prefix . 'orderby=' . $key . '&sort=desc">';
				//$table .= '<img class="sort-icon" src="' . RACINE_SITE . 'images/arrow_down.png" alt="Tri décroissant" />';
				$table .= '<span class="glyphicon glyphicon-chevron-down aria-hidden="true">';
				$table .= '</a>';
			}

			$table .= '</th>';
		}


		$table .= '</tr>';
		$table .= '</thead>';

		if($donnees)
		{
			$table .= '<tbody>';

			foreach($donnees as $ligne)
			{
				$table .= '<tr>';
				foreach($ligne as $key => $value)
				{
					$original_value = $value;

					if($key == 'photo')
						$table .= '<td><img class="img-responsive" src="' . RACINE_SITE . $value . '" alt="Photo" /></td>';
					elseif($key == 'description' || $key == 'commentaire')
					{
						if(strlen($value) > 50)
						{
							$value = $this->tronquerTexte($value, 20);
							$value = wordwrap($value, $text_limit, "\n");
						}

						$table .= '<td title="' . $original_value . '">' . $value . '</td>';
					}
					else
						$table .= '<td>' . $value . '</td>';
				}

				if($edit)
				{
					$ligne->rewind();
					//$table .= '<td class="my-icon"><a href="?action=edit&' . $ligne->key() . '=' . $ligne->current() . '"><img src="' . RACINE_SITE . 'images/edit_icon.png" alt="Editer la ligne" /></a></td>';
					$table .= '<td class="my-icon"><a href="?action=edit&' . $ligne->key() . '=' . $ligne->current() . '"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a></td>';
				}

				if($delete)
				{
					$ligne->rewind();
					//$table .= '<td class="my-icon"><a href="?action=delete&' . $ligne->key() . '=' . $ligne->current() . '"><img src="' . RACINE_SITE . 'images/trash_icon.png" alt="Supprimer la ligne" /></a></td>';
					$table .= '<td class="my-icon"><a href="?action=delete&' . $ligne->key() . '=' . $ligne->current() . '"><span class="glyphicon glyphicon-trash" aria-hidden="true"></a></td>';
				}

				$table .= '</tr>';
			}

			$table .= '</tbody>';
		}
		else
		{
			$table .= '<tfoot>';
			$table .= '<tr><td colspan="' . count($entetes) . '" class="text-center"><em>Aucune donnée existante.</em></td></tr>';
			$table .= '</tfoot>';
		}

		$table .= '</table>';
		$table .= '</div>';

		return $table;
	}


	private function tronquerTexte($text, $limit)
	{
		$cut = substr($text, 0, $limit);
		$lastSpacePos = strrpos($cut, ' ');
		$text = substr($cut, 0, $lastSpacePos - 1);
		$text .= '...';

		return $text;
	}
}