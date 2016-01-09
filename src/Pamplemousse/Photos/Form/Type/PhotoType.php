<?php

namespace Pamplemousse\Photos\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextAreaType;

class PhotoType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id', HiddenType::class);
        $builder->add('description', TextAreaType::class, [ 
            'attr' => ['placeholder' => 'Description'],
            'required' => false
        ]);
        $builder->add('is_favorite', CheckboxType::class, [
            'label' => "Marquer comme favori",
            'required' => false
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $data = $form->getData();
        $label = ($data->description) ? $data->description : $data->filename;
        $view->vars['name'] = $label;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Pamplemousse\Photos\Entity\Photo',
        ));
    }

    public function getParent()
    {
        return FormType::class;
    }

}
