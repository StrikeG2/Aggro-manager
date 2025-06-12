<?php

namespace App\Form;

use App\Entity\MouvementProduit;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;


class MouvementProduitForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];
        
        $builder
            ->add('produit', EntityType::class, [
                'class' => Produit::class,
                'choices' => $user->getProduits(),
                'choice_label' => 'nom',
                'label' => 'Produit'
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Entrée' => 'entree',
                    'Sortie' => 'sortie'
                ],
                'label' => 'Type de mouvement'
            ])
            ->add('quantite', NumberType::class, [
                'label' => 'Quantité'
            ])
            ->add('prixUnitaire', NumberType::class, [
                'label' => 'Prix unitaire (FCFA)'
            ])
            ->add('date', DateTimeType::class, [
                'label' => 'Date et heure',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker'],
                'data' => new \DateTimeImmutable() // Valeur par défaut = maintenant
            ])
            ->add('motif', TextareaType::class, [
                'label' => 'Motif',
                'required' => false
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MouvementProduit::class,
            'user' => null, // Ajoutez cette ligne pour définir l'option
        ]);
    }
}