<?php

/**
 * @file
 * Contains \Drupal\multistep_example\Plugin\Block\MultistepExampleBlock.
 */

namespace Drupal\multistep_example\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormBuilder;

/**
 * Provides the Multi step example block.
 *
 * @Block(
 *   id = "multistep_example",
 *   admin_label = @Translation("Block Multi step example")
 * )
 */
class MultistepExampleBlock extends BlockBase implements ContainerFactoryPluginInterface {

  protected FormBuilder $formBuilder;

  /**
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param FormBuilder $formBuilder
   */
  public function __construct(
    array $configuration, $plugin_id, $plugin_definition,
    FormBuilder $formBuilder
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->formBuilder = $formBuilder;
  }

  /**
   * @param ContainerInterface $container
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   *
   * @return static
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('form_builder')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = $this->formBuilder?->getForm(\Drupal\multistep_example\Form\MultistepExample\MultistepExampleForm::class);
    return $form ?? [];
  }

}
