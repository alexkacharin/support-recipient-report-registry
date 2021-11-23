<?php
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('@dektrium/rbac', dirname(dirname(__DIR__)) . '/common/modules/yii2-rbac');
Yii::setAlias('@dektrium/user', dirname(dirname(__DIR__)) . '/common/modules/yii2-user');
Yii::setAlias('@Matodor/Common', dirname(dirname(__DIR__)) . '/common/modules/matodor/common');
Yii::setAlias('@Matodor/RegistryConstructor', dirname(dirname(__DIR__)) . '/common/modules/matodor/registry-constructor');
