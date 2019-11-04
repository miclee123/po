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
        for ($i = 0; $i < random_int(2, 10); $i++) {
            $createdAtUnixTime = random_int(time() - 3600 * 24 * 180, time()); // полгода яблоку хватит, чтобы повисеть, упасть и сгнить.
            $apple = new Apple;
            $apple->color = $colors[random_int(0, 2)];
            $apple->created_at = date('Y.m.d H:i:s', $createdAtUnixTime);
            $apple->status = $statuses[random_int(0, 1)];
            $startFellTimeRange = $createdAtUnixTime < (time() - 3600 * 24 * 10) ? time() - 3600 * 24 * 10 : $createdAtUnixTime; // иначе слишком редко будут попадаться съедобные
            $apple->fell_at = $apple->hands() ? null : date('Y.m.d H:i:s', random_int($startFellTimeRange, time()));
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

    /**
     * @return bool
     */
    public function hands()
    {
        return $this->status == self::STATUS_HANGS;
    }

    /**
     * @return bool
     */
    public function allowedToEat()
    {
        return $this->status == self::STATUS_FELL
            && !is_null($this->fell_at)
            && (time() - strtotime($this->fell_at) <= 3600 * 24 * 5);
    }

    /**
     * @return bool
     */
    public function isRotten()
    {
        return !$this->hands() && !$this->allowedToEat();
    }

    public function renderApple()
    {
        $marginTop = $this->hands() ? '300px' : '700px';
        if ($this->isRotten()) {
            $actionFormHtml = 'Яблоко сгнило';
        } elseif ($this->allowedToEat()) {
            $actionFormHtml = Html::beginForm('', 'post', ['data' => ['pjax' => 1]]) .
                Html::hiddenInput('action', 'eat') .
                Html::hiddenInput('id', $this->id) .
                Html::tag('p', 'Сколько процентов хотите откусить?') .
                Html::input('number', 'eat_percent', null, [
                    'min' => 1,
                    'max' => 100 - $this->eaten_percent,
                    'class' => 'form-control',
                    'placeholder' => 'Доступно ' . (100 - $this->eaten_percent) . '%',
                    'required' => true
                ]) .
                Html::tag('br') .
                Html::submitButton('Откусить', [
                    'class' => 'btn btn-primary'
                ]) .
                Html::endForm();
        } else {
            $actionFormHtml = Html::beginForm('', 'post', ['data' => ['pjax' => 1]]) .
                Html::hiddenInput('action', 'fallToGround') .
                Html::hiddenInput('id', $this->id) .
                Html::submitButton('Сбросить яблоко', [
                    'class' => 'btn btn-primary'
                ]) .
                Html::endForm();
        }
        $actionFormWrap = Html::tag('div', $actionFormHtml, [
            'class' => 'action-form-wrap',
        ]);
        $style = ['margin' => $marginTop . ' auto 0 auto'];
        if ($this->isRotten()) {
            $style['background-color'] = 'brown';
        } else {
            $linearGradient = implode(', ', [
                '90deg',
                'white 0%',
                'white ' . $this->eaten_percent . '%',
                $this->color .' ' . $this->eaten_percent . '%',
                $this->color .' 100%',
            ]);
            $style['background'] = 'linear-gradient(' . $linearGradient . ')';
        }
        return Html::tag('div', $actionFormWrap, [
            'style' => $style,
            'class' => 'apple-item'
        ]);
    }

    public function fallToGround()
    {
        if ($this->hands()) {
            $this->status = self::STATUS_FELL;
            $this->fell_at = date('Y.m.d H:i:s');
            $this->save();
            return true;
        }
        throw new \yii\base\Exception('Яблоко уже лежит');
    }

    public function eat(int $percent)
    {
        if ($this->hands()) {
            throw new \yii\base\Exception('Нельзя есть висящее яблоко');
        }
        if ($this->isRotten()) {
            throw new \yii\base\Exception('Нельзя есть гнилое яблоко яблоко');
        }
        if ($percent + $this->eaten_percent > 100) {
            throw new \yii\base\Exception('Нельзя откусить больше, чем осталось');
        }
        $this->eaten_percent += $percent;
        if ($this->eaten_percent >= 100) {
            $this->delete();
        } else {
            $this->save();
        }
    }
}
