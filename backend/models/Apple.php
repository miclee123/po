<?php

namespace backend\models;

use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\Html;

/**
 * This is the model class for table "apple".
 *
 * @property int $id ID
 * @property string $color Цвет
 * @property string $created_at Дата создания
 * @property string $fell_at Дата падения
 * @property int $status Статус
 * @property int $eaten_percent Съедено %
 */
class Apple extends ActiveRecord
{
    const COLOR_YELLOW = 'yellow';
    const COLOR_GREEN = 'green';
    const COLOR_RED = 'red';

    const STATUS_HANGS = 1;
    const STATUS_FELL = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'apple';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['color', 'created_at', 'status', 'eaten_percent'], 'required'],
            [['status', 'eaten_percent'], 'integer'],
            [['created_at', 'fell_at'], 'safe'],
            [['color'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'color' => 'Цвет',
            'created_at' => 'Дата создания',
            'fell_at' => 'Дата падения',
            'status' => 'Статус',
            'eaten_percent' => 'Съедено %',
        ];
    }

    /**
     * Удаляет старые яблоки и создает новые
     *
     * @throws Exception
     */
    public static function generateRandomSet()
    {
        Apple::getDb()->createCommand('truncate table ' . Apple::tableName())->execute();
        $colors = [Apple::COLOR_GREEN, Apple::COLOR_RED, Apple::COLOR_YELLOW];
        $statuses = [Apple::STATUS_HANGS, Apple::STATUS_FELL];
        $apples = [];
        for ($i = 0;$i < random_int(2, 10);$i++) {
            $createdAtUnixTime = random_int(time() - 3600 * 24 * 180, time()); // полгода яблоку хватит, чтобы повисеть, упасть и сгнить.
            $apple = new Apple;
            $apple->color = $colors[random_int(0, 2)];
            $apple->created_at = date('Y.m.d H:i:s', $createdAtUnixTime);
            $apple->status = $statuses[rand(0, 1)];
            if ($apple->status == Apple::STATUS_FELL) {
                $apple->fell_at = date('Y.m.d H:i:s', random_int($createdAtUnixTime, time()));
            } else {
                $apple->fell_at = null;
            }
            $apple->eaten_percent = 0;
            if ($apple->save()) {
                $apples[] = $apple;
            }
        }
    }

    /**
     * Функция-помощник на время разработки
     *
     * @param $var
     */
    public static function nicePrint($var)
    {
        if (is_bool($var)) {
            $var = 'bool: ' . ($var ? 'true' : 'false');
        }
        echo Html::tag('pre', print_r($var, true), [
            'style' => [
                'border' => '2px solid red',
                'background-color' => 'white'
            ]
        ]);
    }
}
