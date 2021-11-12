<?php

use Matodor\Common\components\Helper;
use yii\helpers\Html;

/** @var \yii\web\View $this view component instance */
/** @var \yii\mail\MessageInterface $message the message being composed */
/** @var string $content View render result */

?>

<?php $this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>" />
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
<body>
    <?php $this->beginBody() ?>
        <div style="background-color:#f7f7f7;font-family:'tahoma' , 'verdana';margin:0;min-width:100%;padding:0">
            <div style="margin:0;table-layout:fixed;width:100%">
                <table cellpadding="0" cellspacing="0" width="100%" style="border-collapse:separate !important;width:100%">
                    <tbody>
                        <tr>
                            <td align="center" height="100%" valign="top" style="background-color:#f7f7f7;padding:0">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:separate;width:100%">
                                    <tbody>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td width="560" style="background-color:#f7f7f7;padding-left:0px;padding-right:0px;width:560px">
                                                <div style="max-width:560px">
                                                    <table align="center" style="border-spacing: 0; color: #333333; font-family: 'tahoma', 'verdana'; margin: 0 auto 0 auto; max-width: 560px; width: 100%;">
                                                        <tbody>
                                                            <tr><td height="30">&nbsp;</td></tr>
                                                            <tr style="width:560px">
                                                                <td align="center" valign="top" width="560" style="color:#333333;font:16px 'tahoma' , 'verdana'">
                                                                    <a href="<?= Helper::to(['site/index'], 'frontend', true) ?>" style="color:#14295e;font-family:'tahoma' , 'verdana'" target="_blank" rel="noopener noreferrer">
                                                                        <img alt="SCRAP REPORTS" height="280" width="560" style="border-width:0; max-width: 560px" src="<?= Helper::to('/images/mail_logo.png', null, true) ?>">
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                            <tr><td height="25">&nbsp;</td></tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                            <td>&nbsp;</td>
                                        </tr>
                                    </tbody>
                                </table>

                                <!-- БЛОК С КОНТЕНТОМ -->
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:separate;width:100%">
                                    <tbody>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td width="560" style="background-color:#ffffff;border-bottom-color:#e7e7e7;border-bottom-style:solid;border-bottom-width:1px;border-left-color:#e9e9e9;border-left-style:solid;border-left-width:1px;border-radius:5px;border-right-color:#e9e9e9;border-right-style:solid;border-right-width:1px;padding-left:0px;padding-right:0px;width:560px;">
                                                <div style="line-height:24px;max-width:560px">
                                                    <table align="center" style="border-spacing:0;color:#333333;font-family:'tahoma' , 'verdana';margin:0 auto 0 auto;max-width:560px">
                                                        <tbody>
                                                            <tr>
                                                                <td width="30">&nbsp;</td>
                                                                <td style="padding:0">
                                                                    <table style="border-spacing:0;color:#333333;font-family:'tahoma' , 'verdana'">
                                                                        <tbody>
                                                                            <tr><td height="40">&nbsp;</td></tr>
                                                                            <tr style="display:block;line-height:28px">
                                                                                <td align="justify" style="padding:0px;text-align:left">
                                                                                    <?= $content ?>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                                <td width="30">&nbsp;</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <table align="center" width="100%" style="border-spacing:0;color:#333333;font-family:'tahoma' , 'verdana';margin:0 auto 0 auto;max-width:560px">
                                                        <tbody>
                                                            <tr>
                                                                <td width="40">&nbsp;</td>
                                                                <td style="padding:0">
									                                <table width="100%" style="border-spacing:0;color:#333333;font-family:'tahoma' , 'verdana'">
                                                                        <tbody>
                                                                            <tr><td height="45">&nbsp;</td></tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                                <td width="40">&nbsp;</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                            <td>&nbsp;</td>
                                        </tr>
                                    </tbody>
                                </table>

                                <!-- НИЗ ПИСЬМА -->
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:separate;width:100%">
                                    <tbody>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td width="560" style="background-color:#f7f7f7;padding-left:0px;padding-right:0px;width:560px">
                                                <div style="line-height:14px;max-width:560px">
                                                    <table align="center" style="border-spacing:0;color:#909090;font-family:'tahoma' , 'verdana';max-width:560px;width:100%">
                                                        <tbody>
                                                            <tr>
                                                                <td width="10">&nbsp;</td>
                                                                <td style="padding:0">
                                                                    <table width="100%" style="border-spacing:0;color:#909090;font-family:'tahoma' , 'verdana'">
                                                                        <tbody>
                                                                            <tr></tr>
                                                                            <tr><td height="8">&nbsp;</td></tr>
                                                                            <tr style="line-height:18px"></tr>
                                                                            <tr><td height="40">&nbsp;</td></tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                                <td width="10">&nbsp;</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </td>
                                            <td>&nbsp;</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
