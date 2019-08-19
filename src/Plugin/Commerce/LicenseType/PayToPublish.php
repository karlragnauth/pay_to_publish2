<?php

namespace Drupal\commerce_license_pay_to_publish\Plugin\Commerce\LicenseType;

use Drupal\commerce_license\Entity\LicenseInterface;
use Drupal\commerce_license\Plugin\Commerce\LicenseType\LicenseTypeBase;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;

use Drupal\entity\BundleFieldDefinition;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a license type controlling a field value on a target entity.
 * Intended for use with permissions_by_term taxonomy field to control acl.
 *
 * @CommerceLicenseType(
 *  id = "commerce_license_pay_to_publish",
 *  label = @Translation("Pay to Publish"),
 *  activation_order_state = "complete",
 * )
 */
class PayToPublish extends LicenseTypeBase implements ContainerFactoryPluginInterface
{

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
    {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildLabel(LicenseInterface $license)
    {
				// Indicate the list option in the license label
        $label = $license->product_variation->entity->getTitle();

        if ($target_entity = $this->fetchTargetEntity($license)) {

        		// Followed by the title from the target entity.
            $target_entity_title = $target_entity->getTitle();
						$label .= ": $target_entity_title";
        } 
        return $label;
    }

    /**
     * {@inheritdoc}
     */
    public function defaultConfiguration()
    {
        return [
        'license_target_field' => 'field_pay_to_publish',
        'license_target_value' => '2',
        ] + parent::defaultConfiguration();
    }

    /**
     * Fetches the target entity to be controlled by the license
     */
    public function fetchTargetEntity(LicenseInterface $license)
    {
        $license_id = $license->id();

        // Get the order item matching this license.
        $query = \Drupal::entityTypeManager()
            ->getStorage('commerce_order_item')->getQuery()
            ->condition('license', $license_id);
        $result = $query->execute();

        // Assume there will only ever be one match and warn if no target found.
        if ($order_item_id = key($result)) {
            $order_item = entity_load('commerce_order_item', $order_item_id);
            // Get entity from order item.
             $entity = $order_item->field_target_entity->entity;

            return $entity;
        } else {
            drupal_set_message("Unable to load pay to publish target entity for license id $license_id.", $type = 'error');
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setConfigurationValuesOnLicense(LicenseInterface $license)
    {
        $license->license_target_field = $this->configuration['license_target_field'];
        $license->license_target_value = $this->configuration['license_target_value'];
    }

    /**
     * {@inheritdoc}
     */
    public function grantLicense(LicenseInterface $license)
    {
        // Get the entity that this license targets.
        if ($target_entity = $this->fetchTargetEntity($license)) {

            // Get the field to set and the value to set on it.
            $target_field_name = $license->license_target_field->value;
            $target_value = $license->license_target_value->value;

            // Set the value.
            $target_entity->{$target_field_name} = $target_value;
            $target_entity->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function revokeLicense(LicenseInterface $license)
    {
        // Get the entity that this license targets.
        if ($target_entity = $this->fetchTargetEntity($license)) {

            // Get the definition of the field to unset.
            $target_field_name = $license->license_target_field->value;
            $bundle_fields = \Drupal::service('entity_field.manager')->getFieldDefinitions($target_entity->getEntityTypeId(), $target_entity->bundle());
            $target_field_definition = $bundle_fields[$target_field_name];
    
            // Set the default value for this field onto the target entity.
            $default_value = $target_field_definition->getDefaultValue($target_entity);
            $target_entity->{$target_field_name} = $default_value;
    
            $target_entity->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildConfigurationForm(array $form, FormStateInterface $form_state)
    {
        $form['license_target_field'] = [
        '#type' => 'textfield',
        '#title' => $this->t("Machine field name on target entity to control with license"),
        '#description' => $this->t("<p>The <em>machine name</em> of the field on the target entity this license will control.</p><p>Usually set to 'field_pay_to_publish' to reference  the default permissions_by_term taxonomy list controlling access on the target entity.</p><p>Be careful, your site will fail if this field does not exist or is set incorrectly. Look it up under Structure-&gt;Content types if unsure.</p>"),
        '#default_value' => $this->configuration['license_target_field'],
        '#required' => true,
        ];
        $form['license_target_value'] = [
        '#type' => 'textfield',
        '#title' => $this->t("Value to set on target field"),
        '#description' => $this->t('<p>The value this license subcription will set on the target entity field while active. Returns to the default value defined on the target entity field upon license revokation.</p><p>Be careful, your site will fail if an incompatible value is specified. eg. You must specify the index value for taxonomy list options, not the label.</p>'),
        '#default_value' => $this->configuration['license_target_value'],
        '#required' => true,
        ];

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function buildFieldDefinitions()
    {
        $fields = parent::buildFieldDefinitions();
        $fields['license_target_field'] = BundleFieldDefinition::create('string')
            ->setLabel(t('Target field name'))
            ->setDescription(t('The machine name of the entity field this license sets a value upon. Be careful, your site will fail if this field does not exist or is incorrect. Look it up under Structure->Content types if unsure.'))
            ->setCardinality(1)
            ->setRequired(true);
        $fields['license_target_value'] = BundleFieldDefinition::create('string')
            ->setLabel(t('Target field value'))
            ->setDescription(t('The value this license will set on the target entity field. Be careful, your site will fail if an incompatible value is specified. eg. You must specify the index value for taxonomy field options, not the label.'))
            ->setCardinality(1)
            ->setRequired(true);

        return $fields;
    }
}
