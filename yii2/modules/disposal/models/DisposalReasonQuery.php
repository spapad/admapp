<?php

namespace app\modules\disposal\models;

/**
 * This is the ActiveQuery class for [[DisposalReason]].
 *
 * @see DisposalReason
 */
class DisposalReasonQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return DisposalReason[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return DisposalReason|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
