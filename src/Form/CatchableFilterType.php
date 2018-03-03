<?php

/*
 * This file is part of the ForciCatchableBundle package.
 *
 * Copyright (c) Forci Web Consulting Ltd.
 *
 * Author Tatyana Mincheva <tatjana@forci.com>
 * Author Martin Kirilov <martin@forci.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Forci\Bundle\Catchable\Form;

use Forci\Bundle\Catchable\Filter\CatchableFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CatchableFilterType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('message', TextType::class, [
                'attr' => [
                    'placeholder' => 'Message'
                ]
            ])
            ->add('file', TextType::class, [
                'attr' => [
                    'placeholder' => 'File'
                ]
            ])
            ->add('class', TextType::class, [
                'attr' => [
                    'placeholder' => 'Class'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => CatchableFilter::class,
            'method' => 'GET'
        ]);
    }
}
