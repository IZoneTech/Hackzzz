<?php
/**
 * Version Plugin
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Version;

use CommonApi\Event\UpdateInterface;
use Molajo\Plugin\UpdateEventPlugin;

/**
 * Version Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class VersionPlugin extends UpdateEventPlugin implements UpdateInterface
{
    /**
     * Pre-create processing
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeCreate()
    {
        $field      = $this->getField('version');
        $name       = $field['name'];
        $fieldValue = $this->getFieldValue($field);
        if ($fieldValue === false
            || $fieldValue == ''
        ) {
            $newFieldValue = 1;
            $this->setField($field, $name, $newFieldValue);
        }

        $field      = $this->getField('version_of_id');
        $name       = $field['name'];
        $fieldValue = $this->getFieldValue($field);
        if ($fieldValue === false
            || $fieldValue == ''
        ) {
            $newFieldValue = 0;
            $this->setField($field, $name, $newFieldValue);
        }

        $field      = $this->getField('status_prior_to_version');
        $name       = $field['name'];
        $fieldValue = $this->getFieldValue($field);
        if ($fieldValue === false
            || $fieldValue == ''
        ) {
            $newFieldValue = 0;
            $this->setField($field, $name, $newFieldValue);
        }

        return $this;
    }

    /**
     * Pre-update processing
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeUpdate()
    {
        $field      = $this->getField('version');
        $name       = $field['name'];
        $fieldValue = $this->getFieldValue($field);
        if ($fieldValue === false
            || $fieldValue == ''
        ) {
            $newFieldValue = 1 + 1;
            $this->setField($field, $name, $newFieldValue);
        }

        $field      = $this->getField('status_prior_to_version');
        $name       = $field['name'];
        $fieldValue = $this->getFieldValue($field);
        if ($fieldValue === false
            || $fieldValue == ''
        ) {
            $newFieldValue = $this->query_results->status;
            $this->setField($field, $name, $newFieldValue);
        }

        return $this;
    }

    /**
     * Post-update processing
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterUpdate()
    {
        return $this;
    }

    /**
     * createVersion
     *
     * Automatic version management save and restore processes for resources
     *
     * @return  $this
     * @since   1.0
     */
    public function createVersion()
    {
        if ($this->get('version_management', 1) == 1) {
        } else {
            return $this;
        }

        /** create **/
        if ((int)$this->get('id') == 0) {
            return $this;
        }

        /** versions deleted with delete **/
        if ($this->get('action') == 'delete'
            && $this->get('retain_versions_after_delete', 1) == 0
        ) {
            return $this;
        }

        /** create version **/
        $versionKey = $this->model->createVersion($this->get('id'));

        /** error processing **/
        if ($versionKey === false) {
            // redirect error
            return $this;
        }

        /** Plugin_Event: onContentCreateVersion
         **/

        return $this;
    }

    /**
     * maintainVersionCount
     *
     * Prune version history, if necessary
     *
     * @return  $this
     */
    public function maintainVersionCount()
    {
        if ($this->get('version_management', 1) == 1) {
        } else {
            return $this;
        }

        /** no versions to delete for create **/
        if ((int)$this->get('id') == 0) {
            return $this;
        }

        /** versions deleted with delete **/
        if ($this->get('action') == 'delete'
            && $this->get('retain_versions_after_delete', 1) == 0
        ) {
            $maintainVersions = 0;
        } else {
            /** retrieve versions desired **/
            $maintainVersions = $this->get('maintain_version_count', 5);
        }

        /** delete extra versions **/
        $results = $this->model->maintainVersionCount($this->get('id'), $maintainVersions);

        /** version delete failed **/
        if ($results === false) {
            // redirect false
            return $this;
        }

        /** Plugin_Event: onContentMaintainVersions
         **/

        return $this;
    }
}
