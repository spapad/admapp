<?php
namespace app\modules\SubstituteTeacher\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use app\modules\SubstituteTeacher\traits\Selectable;

//use spapad\yii2helpers\validators\DefaultOnOtherAttributeValidator;

/**
 * This is the model class for table "{{%stposition}}".
 *
 * @property integer $id
 * @property string $title
 * @property integer $school_type Defualts to 1, if 2 denotes KEDDY
 * @property integer $operation_id
 * @property integer $specialisation_id
 * @property integer $prefecture_id
 * @property integer $teachers_count
 * @property integer $hours_count
 * @property integer $whole_teacher_hours
 * @property integer $covered_teachers_count
 * @property integer $covered_hours_count
 * @property string $created_at
 * @property string $updated_at
 * @property boolean $sign_language
 *
 * @property CallPosition[] $callPositions
 * @property Prefecture $prefecture
 * @property Operation $operation
 * @property Specialisation $specialisation
 */
class Position extends \yii\db\ActiveRecord
{
    use Selectable;

    const POSITION_TYPE_TEACHER = 1;
    const POSITION_TYPE_HOURS = 0;

    const NO_PREFERENCES = 0;
    const SIGN_LANGUAGE_PREFER = 1;
    const SIGN_LANGUAGE_INDIFFERENT = 0;

    const SCHOOL_TYPE_DEFAULT = 1;
    const SCHOOL_TYPE_KEDDY = 2;

    public $position_has_type;
    public $position_has_type_label; // use to select teachers or hours count
    public $school_type_label;
    public $covered;
    public $remaining;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%stposition}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['school_type', 'default', 'value' => Position::SCHOOL_TYPE_DEFAULT],
            ['school_type', 'filter', 'filter' => 'intval'],
            [['operation_id', 'title', 'whole_teacher_hours', 'school_type'], 'required'],
            ['school_type', 'in', 'range' => [self::SCHOOL_TYPE_DEFAULT, self::SCHOOL_TYPE_KEDDY]],
            [['teachers_count', 'covered_teachers_count', 'hours_count', 'covered_hours_count', 'sign_language'], 'default', 'value' => 0],
//            [['position_has_type'], DefaultOnOtherAttributeValidator::className(),
//                'if' => '0', 'replace' => true, 'otherAttributeValue' => 0, 'otherAttribute' => 'teachers_count'],
//            [['position_has_type'], DefaultOnOtherAttributeValidator::className(),
//                'if' => '0', 'replace' => true, 'otherAttributeValue' => 0, 'otherAttribute' => 'covered_teachers_count'],
            ['position_has_type', 'safe'],
            [
                ['teachers_count', 'covered_teachers_count'], 'filter',
                'filter' => function ($value) {
                    return 0;
                },
                'when' => function ($model) {
                    return $model->position_has_type == Position::POSITION_TYPE_HOURS;
                }
            ],
//            [['position_has_type'], DefaultOnOtherAttributeValidator::className(),
//                'if' => '1', 'replace' => true, 'otherAttributeValue' => 0, 'otherAttribute' => 'hours_count'],
//            [['position_has_type'], DefaultOnOtherAttributeValidator::className(),
//                'if' => '1', 'replace' => true, 'otherAttributeValue' => 0, 'otherAttribute' => 'covered_hours_count'],
            [
                ['hours_count', 'covered_hours_count'], 'filter',
                'filter' => function ($value) {
                    return 0;
                },
                'when' => function ($model) {
                    return $model->position_has_type == Position::POSITION_TYPE_TEACHER;
                }
            ],
            [['teachers_count', 'covered_teachers_count'], 'required',
                'when' => function ($model) {
                    return $model->position_has_type == Position::POSITION_TYPE_TEACHER;
                }
            ],
            [['hours_count', 'covered_hours_count'], 'required',
                'when' => function ($model) {
                    return $model->position_has_type == Position::POSITION_TYPE_HOURS;
                }
            ],
            [['teachers_count', 'covered_teachers_count', 'hours_count', 'covered_hours_count'], 'integer', 'min' => 0],
            [['created_at', 'updated_at'], 'safe'],
            [['operation_id', 'specialisation_id', 'prefecture_id', 'whole_teacher_hours'], 'integer'],
            [['title'], 'string', 'max' => 500],
            [['prefecture_id'], 'exist', 'skipOnError' => true, 'targetClass' => Prefecture::className(), 'targetAttribute' => ['prefecture_id' => 'id']],
            [['operation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Operation::className(), 'targetAttribute' => ['operation_id' => 'id']],
            [['specialisation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Specialisation::className(), 'targetAttribute' => ['specialisation_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()')
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('substituteteacher', 'ID'),
            'title' => Yii::t('substituteteacher', 'Title'),
            'school_type' => Yii::t('substituteteacher', 'School type'),
            'operation_id' => Yii::t('substituteteacher', 'Operation'),
            'specialisation_id' => Yii::t('substituteteacher', 'Specialisation'),
            'prefecture_id' => Yii::t('substituteteacher', 'Prefecture'),
            'teachers_count' => Yii::t('substituteteacher', 'Teachers Count'),
            'hours_count' => Yii::t('substituteteacher', 'Hours Count'),
            'whole_teacher_hours' => Yii::t('substituteteacher', 'Whole Teacher Hours'),
            'covered_teachers_count' => Yii::t('substituteteacher', 'Covered Teachers Count'),
            'covered_hours_count' => Yii::t('substituteteacher', 'Covered Hours Count'),
            'created_at' => Yii::t('substituteteacher', 'Created At'),
            'updated_at' => Yii::t('substituteteacher', 'Updated At'),
            'position_has_type' => Yii::t('substituteteacher', 'Position has...'),
            'covered' => Yii::t('substituteteacher', 'Covered'),
            'remaining' => Yii::t('substituteteacher', 'Remaining'),
            'sign_language' => Yii::t('substituteteacher', 'Sign language preference'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCallPositions()
    {
        return $this->hasMany(CallPosition::className(), ['position_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrefecture()
    {
        return $this->hasOne(Prefecture::className(), ['id' => 'prefecture_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperation()
    {
        return $this->hasOne(Operation::className(), ['id' => 'operation_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpecialisation()
    {
        return $this->hasOne(Specialisation::className(), ['id' => 'specialisation_id']);
    }

    /**
     * Get a list of available choices in the form of
     * ID => LABEL suitable for select lists.
     */
    public static function defaultSelectables($index_property = 'id', $label_property = 'title', $group_property = null)
    {
        return static::selectables($index_property, $label_property, $group_property, function ($aq) {
            return $aq->orderBy(['title' => SORT_DESC]);
        });
    }

    /**
     * Return a list of available school types for selection options
     */
    public static function getSchoolTypeChoices()
    {
        return [
            (string)self::SCHOOL_TYPE_DEFAULT => Yii::t('substituteteacher', 'SCHOOL UNIT'),
            (string)self::SCHOOL_TYPE_KEDDY => Yii::t('substituteteacher', 'SCHOOL KEDDY')
        ];
    }

    public function getSignLanguageLabelHtml()
    {
        return ($this->sign_language ? ' <span class="label label-primary">Νοηματική</span>' : '');
    }
    /**
     * @inheritdoc
     *
     */
    public function afterFind()
    {
        parent::afterFind();
        $this->position_has_type = ($this->teachers_count > 0) ? Position::POSITION_TYPE_TEACHER : Position::POSITION_TYPE_HOURS;
        $this->position_has_type_label = ($this->position_has_type == Position::POSITION_TYPE_TEACHER) ? \Yii::t('substituteteacher', 'Teachers') : \Yii::t('substituteteacher', 'Hours');
        $this->covered = ($this->position_has_type == Position::POSITION_TYPE_TEACHER) ? $this->covered_teachers_count : $this->covered_hours_count;
        $this->remaining = ($this->position_has_type == Position::POSITION_TYPE_TEACHER) ? $this->teachers_count - $this->covered_teachers_count : $this->hours_count - $this->covered_hours_count;
        $this->school_type_label = ($this->school_type == self::SCHOOL_TYPE_KEDDY) ? Yii::t('substituteteacher', 'SCHOOL KEDDY') : Yii::t('substituteteacher', 'SCHOOL UNIT');
    }

    /**
     * @inheritdoc
     * @return PositionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PositionQuery(get_called_class());
    }
}
