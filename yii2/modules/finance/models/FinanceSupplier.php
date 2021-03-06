<?php

namespace app\modules\finance\models;

use app\modules\finance\Module;

/**
 * This is the model class for table "{{%finance_supplier}}".
 *
 * @property integer $suppl_id
 * @property string $suppl_name
 * @property integer $suppl_vat
 * @property string $suppl_address
 * @property integer $suppl_phone
 * @property string $suppl_email
 * @property integer $suppl_fax
 * @property string $suppl_iban
 * @property string $suppl_employerid
 * @property integer $taxoffice_id
 *
 * @property FinanceExpenditure[] $financeExpenditures
 * @property FinanceInvoice[] $financeInvoices
 * @property FinanceTaxoffice $taxoffice
 */
class FinanceSupplier extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%finance_supplier}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['suppl_name', 'suppl_vat', 'suppl_iban', 'suppl_employerid', 'taxoffice_id'], 'required'],
            [['taxoffice_id'], 'integer'],
            [['suppl_name', 'suppl_address', 'suppl_email'], 'string', 'max' => 255],
            [['suppl_email'], 'email'],
            [['suppl_iban'], 'string', 'max' => 27],
            [['suppl_vat','suppl_phone', 'suppl_fax'], 'string', 'max' => 30],
            [['suppl_employerid'], 'string', 'max' => 100],
            [['taxoffice_id'], 'exist', 'skipOnError' => true, 'targetClass' => FinanceTaxoffice::className(), 'targetAttribute' => ['taxoffice_id' => 'taxoffice_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'suppl_id' => Module::t('modules/finance/app', 'Supplier'),
            'suppl_name' => Module::t('modules/finance/app', 'Name'),
            'suppl_vat' => Module::t('modules/finance/app', 'VAT Number'),
            'suppl_address' => Module::t('modules/finance/app', 'Address'),
            'suppl_email' => Module::t('modules/finance/app', 'E-mail'),
            'suppl_phone' => Module::t('modules/finance/app', 'Phone Number'),
            'suppl_fax' => Module::t('modules/finance/app', 'Fax'),
            'suppl_iban' => Module::t('modules/finance/app', 'IBAN'),
            'suppl_employerid' => Module::t('modules/finance/app', 'Employer Registration Number'),
            'taxoffice_id' => Module::t('modules/finance/app', 'Tax Office'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFinanceExpenditures()
    {
        return $this->hasMany(FinanceExpenditure::className(), ['suppl_id' => 'suppl_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFinanceInvoices()
    {
        return $this->hasMany(FinanceInvoice::className(), ['suppl_id' => 'suppl_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaxoffice()
    {
        return $this->hasOne(FinanceTaxoffice::className(), ['taxoffice_id' => 'taxoffice_id']);
    }

    /**
     * @inheritdoc
     * @return FinanceSupplierQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new FinanceSupplierQuery(get_called_class());
    }
}
