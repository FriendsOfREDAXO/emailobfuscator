<?php
$content = '';

if (rex_post('config-submit', 'boolean')) {
    $this->setConfig(rex_post('config', [
        ['autoload_css', 'bool'],
        ['autoload_js', 'bool'],
        ['articles', 'string'],
        ['templates', 'array[int]'],
    ]));
    $content .= rex_view::info($this->i18n('config_saved'));
}

$content .= '<div class="rex-form">';
$content .= '  <form action="' . rex_url::currentBackendPage() . '" method="post">';
$content .= '    <fieldset>';

$formElements = [];

//Start - autoload_css
$n = [];
$n['label'] = '<label for="emailobfuscator-config-autoload_css">' . $this->i18n('config_autoload_css') . '</label>';
$n['field'] = '<input type="checkbox" id="emailobfuscator-config-autoload_css" name="config[autoload_css]" value="1" ' . ($this->getConfig('autoload_css') ? ' checked="checked"' : '') . '>';
$formElements[] = $n;
//End - autoload_css

//Start - autoload_js
$n = [];
$n['label'] = '<label for="emailobfuscator-config-autoload_js">' . $this->i18n('config_autoload_js') . '</label>';
$n['field'] = '<input type="checkbox" id="emailobfuscator-config-autoload_js" name="config[autoload_js]" value="1" ' . ($this->getConfig('autoload_js') ? ' checked="checked"' : '') . '>';
$formElements[] = $n;
//End - autoload_js

$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$assets = $fragment->parse('core/form/checkbox.php');
$formElements = [];

//Start - autoload_note
$n = [];
$n['label'] = $this->i18n('config_autoload_assets');
$n['field'] = $assets;
$n['note'] = rex_i18n::rawMsg('emailobfuscator_config_autoload_note', rex_url::backendPage('packages', ['subpage' => 'help', 'package' => $this->getPackageId()]));
$formElements[] = $n;
//End - autoload_note

//Start - articles
$n = [];
$n = [];
$n['label'] = '<label for="emailobfuscator-config-articles">' . $this->i18n('config_articles') . '</label>';
$n['field'] = rex_var_linklist::getWidget(1, 'config[articles]', $this->getConfig('articles'));
$formElements[] = $n;
//End - articles

//Start - templates
$n = [];
$n['label'] = '<label for="emailobfuscator-config-templates">' . $this->i18n('config_templates') . '</label>';
$select = new rex_select();
$select->setId('emailobfuscator-config-templates');
$select->setMultiple();
$select->setSize(20);
$select->setName('config[templates][]');
$select->addSqlOptions('SELECT `name`, `id` FROM `' . rex::getTablePrefix() . 'template` ORDER BY `name` ASC');
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
$n['field'] = '<input type="submit" name="config-submit" value="' . $this->i18n('config_action_save') . '" ' . rex::getAccesskey($this->i18n('config_action_save'), 'save') . '>';
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
