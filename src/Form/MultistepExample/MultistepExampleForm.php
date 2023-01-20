<?php

namespace Drupal\multistep_example\Form\MultistepExample;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides Multi step example form.
 */
class MultistepExampleForm extends FormBase {

  public const STEPS = ['step1', 'step2', 'step3'];
  protected const STEPS_CLASS = ['StepOne', 'StepTwo', 'StepThree'];
  protected const FORM_WRAPPER_ID = 'multistep-example-wrapper';
  protected array $steps = [];
  protected int $stepId = 1;
  protected int $stepKey = 0;

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return "multistep_example_form";
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $stepId = $this->stepId;
    $this->stepKey = $stepId-1;
    $stepClassName = $this->getStepClassName();
    if (method_exists($this, 'form' . $stepClassName)) {
      $form = call_user_func_array([$this, 'form' . $stepClassName], [&$form, $form_state]);
    }
    if (isset(self::STEPS[$this->stepKey])) {
      $form['#theme'] = 'multistep_example_' .  self::STEPS[$this->stepKey];
    }
    $form['#prefix'] = '<div id="' . self::FORM_WRAPPER_ID . '">';
    $form['#suffix'] = '</div>';
    return $form;
  }

  /**
   * @param array &$form
   * @param FormStateInterface $form_state
   * @return array &$form
   */
  public function formStepOne(array &$form, FormStateInterface $form_state): array {
    $form['step1']['first_name'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('First Name'),
      '#default_value' => $this->steps[1]['first_name'] ?? '',
      '#size' => 60,
      '#maxlength' => 128,
      '#required' => TRUE,
    );
    $form['step1']['actions'] = [
      '#type' => 'container',
      'submit' => [
        '#type' => 'submit',
        '#id' => 'edit-next',
        '#attributes' => ['class' => ['form-submit']],
        '#value' => $this->t('Start'),
        '#ajax' => [
          'callback' => [$this, 'formAjaxHandler'],
          'wrapper' => self::FORM_WRAPPER_ID,
        ],
      ],
    ];
    return $form;
  }

  /**
   * @param array &$form
   * @param FormStateInterface $form_state
   * @return array &$form
   */
  public function formStepTwo(array &$form, FormStateInterface $form_state): array {
    $form['step1']['last_name'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Last Name'),
      '#default_value' => $this->steps[2]['last_name'] ?? '',
      '#size' => 60,
      '#maxlength' => 128,
      '#required' => TRUE,
    );
    $form['step2']['actions'] = [
      '#type' => 'container',
      'back' => [
        '#type' => 'submit',
        '#value' => $this->t('Previous'),
        '#limit_validation_errors' => [],
        '#id' => 'edit-back',
        '#attributes' => ['class' => ['form-submit']],
        '#ajax' => [
          'callback' => [$this, 'formAjaxHandler'],
          'wrapper' => self::FORM_WRAPPER_ID,
        ],
      ],
      'submit' => [
        '#type' => 'submit',
        '#id' => 'edit-next',
        '#attributes' => ['class' => ['form-submit']],
        '#value' => $this->t('Next'),
        '#ajax' => [
          'callback' => [$this, 'formAjaxHandler'],
          'wrapper' => self::FORM_WRAPPER_ID,
        ],
      ],
    ];
    return $form;
  }

  /**
   * @param array &$form
   * @param FormStateInterface $form_state
   * @return array &$form
   */
  public function formStepThree(array &$form, FormStateInterface $form_state): array {
    $first_name = $this->steps[1]['first_name'] ?? '';
    $last_name = $this->steps[2]['last_name'] ?? '';
    $form['step3']['name'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $this->t('Your name is') . ' ' .  $first_name . ' ' . $last_name,
    ];
    $form['step3']['actions'] = [
      '#type' => 'container',
      'back' => [
        '#type' => 'submit',
        '#value' => $this->t('Previous'),
        '#limit_validation_errors' => [],
        '#id' => 'edit-back',
        '#attributes' => ['class' => ['form-submit']],
        '#ajax' => [
          'callback' => [$this, 'formAjaxHandler'],
          'wrapper' => self::FORM_WRAPPER_ID,
        ],
      ],
      'submit' => [
        '#type' => 'submit',
        '#id' => 'finish',
        '#value' => $this->t('Restart'),
        '#attributes' => ['class' => ['form-submit']],
        '#ajax' => [
          'callback' => [$this, 'formAjaxHandler'],
          'wrapper' => self::FORM_WRAPPER_ID,
        ],
      ],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  function validateForm(array &$form, FormStateInterface $form_state) {
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->steps[$this->stepId] = $form_state->getValues();
    $triggering_element = $form_state->getTriggeringElement();
    if ($triggering_element['#id'] == 'edit-back') {
      $this->stepId--;
    }
    elseif ($triggering_element['#id'] == 'edit-next') {
      $this->stepId++;
    }
    else {
      $this->stepId = 1;
      $this->stepKey = 0;
      $this->steps = [];
    }
    $form_state->setRebuild(TRUE);
  }

  /**
   * @param array &$form
   *   Form array.
   * @param FormStateInterface $form_state
   *   Form state interface.
   * @return array $form
   */
  public function formAjaxHandler(array &$form, FormStateInterface $form_state): array  {
    return $form;
  }

  /**
   * @return array|string
   */
  public function getStepClassName(): array|string {
    if (isset(self::STEPS_CLASS[$this->stepKey])) {
      return self::STEPS_CLASS[$this->stepKey];
    }
    return self::STEPS_CLASS[0];
  }

}
