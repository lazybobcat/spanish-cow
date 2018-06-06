<?php
/**
 * This file is part of the spanish-cow project.
 *
 * (c) Nvision S.A.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 * Created by PhpStorm.
 * User: loicb
 * Date: 05/06/18
 * Time: 17:23
 */

namespace App\Form;

use App\Entity\Domain;
use App\Entity\Locale;
use App\Model\Import;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImportFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Domain $domain */
        $domain = $options['domain'];

        $builder
            ->add('localeCode', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices' => $domain->getLocales(),
                'choice_translation_domain' => false,
                'choice_label' => function ($value, $key, $index) {
                    if ($value instanceof Locale) {
                        return $value->getName();
                    }

                    return 'n/a';
                },
                'choice_value' => function ($value) {
                    if ($value instanceof Locale) {
                        return $value->getCode();
                    }

                    return null;
                },
            ])
            ->add('keep', CheckboxType::class, [
                'required' => false,
            ])
            ->add('file', FileType::class, [
                'required' => true,
                'multiple' => false,
            ])
            ->add('sourceType', ChoiceType::class, [
                'required' => true,
                'choices' => Import::getFileTypes(),
                'choice_translation_domain' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Import::class,
            'domain' => Domain::class,
        ]);
    }
}
