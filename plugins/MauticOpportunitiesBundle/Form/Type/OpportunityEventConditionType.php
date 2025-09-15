<?php

namespace MauticPlugin\MauticOpportunitiesBundle\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use MauticPlugin\MauticEventsBundle\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Form type for opportunity event campaign condition.
 */
class OpportunityEventConditionType extends AbstractType
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('event', ChoiceType::class, [
            'label' => 'mautic.opportunities.campaign.condition.event',
            'choices' => $this->getEventChoices(),
            'attr' => [
                'class' => 'form-control',
            ],
            'placeholder' => 'mautic.opportunities.event.select',
            'constraints' => [
                new NotBlank(['message' => 'mautic.opportunities.campaign.condition.event.required']),
            ],
        ]);
    }

    private function getEventChoices(): array
    {
        try {
            $events = $this->entityManager->getRepository(Event::class)
                ->createQueryBuilder('e')
                ->orderBy('e.name', 'ASC')
                ->getQuery()
                ->getResult();

            $choices = [];
            foreach ($events as $event) {
                $choices[$event->getName()] = (string) $event->getId();
            }

            return $choices;
        } catch (\Exception $e) {
            // If there's an error (like table doesn't exist), return empty choices
            return [];
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
        ]);
    }
}