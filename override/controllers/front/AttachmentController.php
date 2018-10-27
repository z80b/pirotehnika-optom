<?php

class AttachmentController extends AttachmentControllerCore
{
    public function postProcess()
    {
        $a = new Attachment(Tools::getValue('id_attachment'), $this->context->language->id);
        if (!$a->id) {
            Tools::redirect('index.php');
        }

        if (Tools::getValue('asimage') == 1) {
            //die('<h1>'._PS_DOWNLOAD_DIR_.$a->file.'</h1>');
            header('Content-Transfer-Encoding: binary');
            header('Content-Type: '.$a->mime);
            header('Content-Length: '.filesize(_PS_DOWNLOAD_DIR_.$a->file));
            $fileContent = file_get_contents(_PS_DOWNLOAD_DIR_.$a->file);
            die($fileContent);

        } else {
            Hook::exec('actionDownloadAttachment', array('attachment' => &$a));

            if (ob_get_level() && ob_get_length() > 0) {
                ob_end_clean();
            }
            header('Content-Transfer-Encoding: binary');
            header('Content-Type: '.$a->mime);
            header('Content-Length: '.filesize(_PS_DOWNLOAD_DIR_.$a->file));
            header('Content-Disposition: attachment; filename="'.utf8_decode($a->file_name).'"');

            @set_time_limit(0);
            readfile(_PS_DOWNLOAD_DIR_.$a->file);
            exit;
        }



    }
}
