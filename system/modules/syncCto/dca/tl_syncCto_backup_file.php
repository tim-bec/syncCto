<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  MEN AT WORK 2011
 * @package    syncCto
 * @license    GNU/LGPL
 * @filesource
 */

$GLOBALS['TL_DCA']['tl_syncCto_backup_file'] = array(
    // Config
    'config' => array
        (
        'dataContainer' => 'Memory',
        'closed' => true,
        'disableSubmit' => false,
        'onload_callback' => array(
            array('tl_syncCto_backup_file', 'onload_callback'),
        ),
        'onsubmit_callback' => array(
            array('tl_syncCto_backup_file', 'onsubmit_callback'),
        ),
        'dcMemory_show_callback' => array(
            array('tl_syncCto_backup_file', 'show_all')
        ),
        'dcMemory_showAll_callback' => array(
            array('tl_syncCto_backup_file', 'show_all')
        ),
    ),
    // Palettes
    'palettes' => array
        (
        'default' => '{backup_legend},backup_type,backup_name;{filelist_legend},filelist;',
    ),
    // Fields
    'fields' => array(
        'backup_type' => array
            (
            'label' => &$GLOBALS['TL_LANG']['tl_syncCto_backup_file']['backup_type'],
            'inputType' => 'select',
            'exclude' => true,
            'reference' => &$GLOBALS['TL_LANG']['SYC'],
            'options_callback' => array('SyncCtoHelper', 'getBackupType'),
        ),
        'filelist' => array
            (
            'label' => &$GLOBALS['TL_LANG']['tl_syncCto_backup_file']['filelist'],
            'exclude' => true,
            'inputType' => 'fileTreeMemory',
            'eval' => array('fieldType' => 'checkbox', 'files' => true, 'filesOnly' => false, 'tl_class' => 'clr'),
        ),
        'backup_name' => array
            (
            'label' => &$GLOBALS['TL_LANG']['tl_syncCto_backup_file']['backup_name'],
            'inputType' => 'text',
            'exclude' => true,
            'eval' => array('maxlength' => 32),
        ),
    )
);

/**
 * Class for backup files
 */
class tl_syncCto_backup_file extends Backend
{

    /**
     * Set new and remove old buttons
     * 
     * @param DataContainer $dc 
     */
    public function onload_callback(DataContainer $dc)
    {
        $dc->removeButton('save');
        $dc->removeButton('saveNclose');

        $arrData = array
            (
            'id' => 'start_backup',
            'formkey' => 'start_backup',
            'class' => '',
            'accesskey' => 'g',
            'value' => specialchars($GLOBALS['TL_LANG']['MSC']['start_backup']),
            'button_callback' => array('tl_syncCto_backup_file', 'onsubmit_callback')
        );

        $dc->addButton('start_backup', $arrData);
    }

    /**
     * Handle backup files configurations
     * 
     * @param DataContainer $dc
     * @return array
     */
    public function onsubmit_callback(DataContainer $dc)
    {
        $arrStepPool = $this->Session->get("SyncCto_File_StepPool");

        if (!is_array($arrStepPool))
        {
            $arrStepPool = array();
        }

        // Check sync. typ
        if (strlen($this->Input->post('backup_type')) != 0)
        {
            if ($this->Input->post('backup_type') == SYNCCTO_FULL || $this->Input->post('backup_type') == SYNCCTO_SMALL)
            {
                $arrStepPool["syncCto_Typ"] = $this->Input->post('backup_type');
            }
            else
            {
                $_SESSION["TL_ERROR"][] = $GLOBALS['TL_LANG']['ERR']['unknown_backup_method'];
                return;
            }
        }
        else
        {
            $arrStepPool["syncCto_Typ"] = SYNCCTO_SMALL;
        }

        $arrStepPool["backup_name"] = $this->Input->post('backup_name', true);
        $arrStepPool["filelist"] = $this->Input->post('filelist', true);
        
        $this->Session->set("SyncCto_File_StepPool", $arrStepPool);

        $this->redirect($this->Environment->base . "contao/main.php?do=syncCto_backups&amp;table=tl_syncCto_backup_file&amp;act=start");
    }

    /**
     * Change active mode to edit
     * 
     * @return string 
     */
    public function show_all($dc, $strReturn)
    {
        return $strReturn . $dc->edit();
    }

}

?>