<?php

namespace Lexik\Bundle\FormFilterBundle\Tests\Fixtures\Filter;

use Lexik\Bundle\FormFilterBundle\Filter\Query\QueryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Form filter for tests.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class ItemCallbackFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'filter_text', array(
            'apply_filter' => array($this, 'fieldNameCallback'),
        ));
        $builder->add('position', 'filter_number', array(
            'apply_filter' => function (QueryInterface $filterQuery, $field, $values) {
                if (!empty($values['value'])) {
                    if ($filterQuery->getExpr() instanceof \Doctrine\MongoDB\Query\Expr) {
                        $expr = $filterQuery->getExpr()->field($field)->notEqual($values['value']);
                    } else {
                        $expr = $filterQuery->getExpr()->neq($field, $values['value']);
                    }

                    return $filterQuery->createCondition($expr);
                }

                return null;
            },
        ));
    }

    public function getName()
    {
        return 'item_filter';
    }

    public function fieldNameCallback(QueryInterface $filterQuery, $field, $values)
    {
        if (!empty($values['value'])) {
            if ($filterQuery->getExpr() instanceof \Doctrine\MongoDB\Query\Expr) {
                $expr = $filterQuery->getExpr()->field($field)->notEqual($values['value']);
            } else {
                $expr = $filterQuery->getExpr()->neq($field, sprintf('\'%s\'', $values['value']));
            }

            return $filterQuery->createCondition($expr);
        }

        return null;
    }
}
