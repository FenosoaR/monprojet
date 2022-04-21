<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File ;

class DemandeDevisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('contact')
            ->add('siteweb')
            ->add('sujet')
            ->add('detail')
            ->add('file' , FileType::class , [
                'label' => 'File : '
                'mapped' =>false,
                'constraints'=> [
                    new File([
                        'maxSize'=>'1024k'
                        'mimeTypes'=>[
                                'application/pdf',
                                'application/vnd.ms-excel',
                                'application/zip',
                                'application/vnd.openxmlformats-officedocument.wordprocess'

                        ],
                        'mimeTypesMessage'=>'Selectionner un fichier '


                    ]) 
                   

                ],


            ])
             ->add('Ajouter', SubmitType::class);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
