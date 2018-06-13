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
 * Date: 07/06/18
 * Time: 17:02
 */

namespace App\Form;

use App\Entity\Domain;
use App\Entity\Locale;
use App\Model\Import;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExportType extends AbstractType
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
                'label' => 'form.locale_code',
            ])
            ->add('targetType', ChoiceType::class, [
                'required' => true,
                'choices' => Import::getFileTypes(),
                'choice_translation_domain' => false,
                'label' => 'form.target_type',
            ])
            ->add('xliffVersion', ChoiceType::class, [
                'required' => true,
                'choices' => Import::getXliffVersions(),
                'choice_translation_domain' => false,
                'label' => 'form.xliff_version',
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
