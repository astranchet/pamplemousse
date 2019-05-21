<?php

namespace Pamplemousse\Photos\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextAreaType;

class PhotoType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', HiddenType::class);
        $builder->add('description', TextAreaType::class, [
            'label' => "Légende", 
            'attr' => ['placeholder' => 'Description'],
            'required' => false
        ]);
        $builder->add('is_favorite', CheckboxType::class, [
            'label' => "Marquer comme favori",
            'required' => false
        ]);
        $builder->add('date_taken', DateTimeType::class, [
            'label' => "Date de prise de vue",
            'input' => 'string',
            'date_widget' => 'single_text',
            'time_widget' => 'choice',
            'html5' => true,
            'required' => true
        ]);

        $cropAlgorithms = \Pamplemousse\Photos\Service::getCropAlgorithms();
        $builder->add('crop_algorithm', ChoiceType::class, [
            'label' => "Choix de la miniature",
            'label_attr' => [ 'class' => 'thumbnail_choice'],
            'choices' => array_combine($cropAlgorithms, $cropAlgorithms),
            'expanded' => true,
        ]);
        $builder->add('kids', ChoiceType::class, [
            'label' => "Qui est-ce ?",
            'choices' => $options['kids'],
            'expanded' => true,
            'multiple' => true
        ]);
        $builder->add('tags', ChoiceType::class, [
            'label' => "Série",
            'choices' => array_flip($options['tags']),
            'expanded' => true,
            'multiple' => true
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $data = $form->getData();
        $label = ($data->description) ? $data->description : "Configuration de ".$data->filename;
        $view->vars['name'] = $label;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Pamplemousse\Photos\Entity\Photo',
            'tags' => [],
            'kids' => []
        ));
    }

    public function getParent()
    {
        return FormType::class;
    }

}
