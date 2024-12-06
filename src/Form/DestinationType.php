<?php

namespace App\Form;

use App\Entity\Destination;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class DestinationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $destination = $options['data'];
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter the name of the destination',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Name cannot be blank.']),
                    new Assert\Length(['max' => 255, 'maxMessage' => 'The name cannot exceed {{ limit }} characters.']),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => true,
                'attr' => [
                    'row' => '5',
                    'class' => 'form-control',
                    'placeholder' => 'Enter a description of the destination',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Description cannot be blank.']),
                    new Assert\Length(['min' => 10, 'minMessage' => 'Description must be at least {{ limit }} characters long.']),
                ],
            ])
            ->add('price', NumberType::class, [
                'label' => 'Price',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter the price of the destination',
                ],
                'constraints' => [
                    new Assert\NotNull(['message' => 'Price cannot be null.']),
                    new Assert\Positive(['message' => 'Price must be a positive number.']),
                ],
            ])
            ->add('duration', TextType::class, [
                'label' => 'Duration',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter the duration of the trip',
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Duration cannot be blank.']),
                    new Assert\Length(['max' => 255, 'maxMessage' => 'The duration cannot exceed {{ limit }} characters.']),
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => !$destination || !$destination->getImage(),
                'attr' => [
                    'accept' => 'image/*',
                    'class' => 'form-control-file',
                ],
                'constraints' => $destination && $destination->getImage()
                    ? [
                        new Assert\Image([
                            'maxSize' => '5M',
                            'maxSizeMessage' => 'The image is too large ({{ size }} {{ suffix }}), the maximum size allowed is {{ limit }} {{ suffix }}.',
                        ])
                    ]
                    : [
                        new Assert\Image([
                            'maxSize' => '5M',
                            'maxSizeMessage' => 'The image is too large ({{ size }} {{ suffix }}), the maximum size allowed is {{ limit }} {{ suffix }}.',
                        ]),
                        new Assert\NotNull(['message' => 'Please upload an image for the destination.']),
                    ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Destination::class,
        ]);
    }
}
