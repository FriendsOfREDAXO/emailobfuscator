<?php
	$content = '';
	
	if (rex_post('config-submit', 'boolean')) {
		$this->setConfig(rex_post('config', [
			['articles', 'array[int]'],
			['templates', 'array[int]'],
		]));
		$content .= rex_view::info($this->i18n('config_saved'));
	}
	
	$content .= '<div class="rex-form">';
	$content .= '  <form action="'.rex_url::currentBackendPage().'" method="post">';
	$content .= '    <fieldset>';
	
	$formElements = [];
	
	//Start - articles
		$n = [];
		$n['label'] = '<label for="rex_emailobfuscator-config-articles">'.$this->i18n('config_articles').'</label>';
		$select = new rex_select();
		$select->setId('rex_emailobfuscator-config-articles');
		$select->setMultiple();
		$select->setSize(20);
		$select->setName('config[articles][]');
		$select->addSqlOptions('SELECT IF(`catname` != \'\', concat(`catname`,\' > \',`name`), `name`) as `article`,`id` FROM `'.rex::getTablePrefix().'article` WHERE `clang_id` = 1 ORDER BY `article` ASC');
		$select->setSelected($this->getConfig('articles'));
		$n['field'] = $select->get();
		$formElements[] = $n;
	//End - articles
	
	//Start - templates
		$n = [];
		$n['label'] = '<label for="rex_emailobfuscator-config-templates">'.$this->i18n('config_templates').'</label>';
		$select = new rex_select();
		$select->setId('rex_emailobfuscator-config-templates');
		$select->setMultiple();
		$select->setSize(20);
		$select->setName('config[templates][]');
		$select->addSqlOptions('SELECT `name`, `id` FROM `'.rex::getTablePrefix().'template` ORDER BY `name` ASC');
		$select->setSelected($this->getConfig('templates'));
		$n['field'] = $select->get();
		$formElements[] = $n;
	//End - templates
		
	$fragment = new rex_fragment();
	$fragment->setVar('elements', $formElements, false);
	$content .= $fragment->parse('core/form/form.php');
	
	$content .= '    </fieldset>';
	
	$content .= '    <fieldset class="rex-form-action">';
	
	$formElements = [];
	
	$n = [];
	$n['field'] = '<input type="submit" name="config-submit" value="'.$this->i18n('config_action_save').'" '.rex::getAccesskey($this->i18n('config_action_save'), 'save').'>';
	$formElements[] = $n;
	
	$fragment = new rex_fragment();
	$fragment->setVar('elements', $formElements, false);
	$content .= $fragment->parse('core/form/submit.php');
	
	$content .= '    </fieldset>';
	$content .= '  </form>';
	$content .= '</div>';
	
	$fragment = new rex_fragment();
	$fragment->setVar('class', 'edit');
	$fragment->setVar('title', $this->i18n('config'));
	$fragment->setVar('body', $content, false);
	echo $fragment->parse('core/page/section.php');
?>