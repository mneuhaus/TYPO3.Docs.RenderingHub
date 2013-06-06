<?php
namespace TYPO3\Docs\Domain\Repository;

/*                                                                        *
 * This script belongs to the FLOW3 framework.                            *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * The FLOW3 default Repository
 *
 * @api
 */
abstract class AbstractRepository implements \TYPO3\FLOW3\Persistence\RepositoryInterface {

	/**
	 * Warning: if you think you want to set this,
	 * look at RepositoryInterface::ENTITY_CLASSNAME first!
	 *
	 * @var string
	 */
	protected $entityClassName;

	/**
	 * Initializes a new Repository.
	 */
	public function __construct() {
		if (static::ENTITY_CLASSNAME === NULL) {
			$this->entityClassName = preg_replace(array('/\\\Repository\\\/', '/Repository$/'), array('\\Model\\', ''), get_class($this));
		} else {
			$this->entityClassName = static::ENTITY_CLASSNAME;
		}
	}

	/**
	 * Returns the classname of the entities this repository is managing.
	 *
	 * Note that anything that is an "instanceof" this class is accepted
	 * by the repository.
	 *
	 * @return string
	 * @api
	 */
	public function getEntityClassName() {
		return $this->entityClassName;
	}

	/**
	 * Adds an object to this repository.
	 *
	 * @param object $object The object to add
	 * @return void
	 * @api
	 */
	public function add($object) {
		// TODO: Implement add() method.
	}

	/**
	 * Removes an object from this repository.
	 *
	 * @param object $object The object to remove
	 * @return void
	 * @api
	 */
	public function remove($object) {
		// TODO: Implement remove() method.
	}

	/**
	 * Returns all objects of this repository.
	 *
	 * @return \TYPO3\FLOW3\Persistence\QueryResultInterface The query result
	 * @api
	 */
	public function findAll() {
		// TODO: Implement findAll() method.
	}

	/**
	 * Finds an object matching the given identifier.
	 *
	 * @param mixed $identifier The identifier of the object to find
	 * @return object The matching object if found, otherwise NULL
	 * @api
	 */
	public function findByIdentifier($identifier) {
		// TODO: Implement findByIdentifier() method.
	}

	/**
	 * Returns a query for objects of this repository
	 *
	 * @return \TYPO3\FLOW3\Persistence\QueryInterface
	 * @api
	 */
	public function createQuery() {
		// TODO: Implement createQuery() method.
	}

	/**
	 * Counts all objects of this repository
	 *
	 * @return integer
	 * @api
	 */
	public function countAll() {
		// TODO: Implement countAll() method.
	}

	/**
	 * Removes all objects of this repository as if remove() was called for
	 * all of them.
	 *
	 * @return void
	 * @api
	 */
	public function removeAll() {
		// TODO: Implement removeAll() method.
	}

	/**
	 * Sets the property names to order results by. Expected like this:
	 * array(
	 *  'foo' => \TYPO3\FLOW3\Persistence\QueryInterface::ORDER_ASCENDING,
	 *  'bar' => \TYPO3\FLOW3\Persistence\QueryInterface::ORDER_DESCENDING
	 * )
	 *
	 * @param array $defaultOrderings The property names to order by by default
	 * @return void
	 * @api
	 */
	public function setDefaultOrderings(array $defaultOrderings) {
		// TODO: Implement setDefaultOrderings() method.
	}

	/**
	 * Schedules a modified object for persistence.
	 *
	 * @param object $object The modified object
	 * @return void
	 * @api
	 */
	public function update($object) {
		// TODO: Implement update() method.
	}

	/**
	 * Magic call method for repository methods.
	 * Provides three methods
	 *  - findBy<PropertyName>($value, $caseSensitive = TRUE)
	 *  - findOneBy<PropertyName>($value, $caseSensitive = TRUE)
	 *  - countBy<PropertyName>($value, $caseSensitive = TRUE)
	 *
	 * @param string $method Name of the method
	 * @param array $arguments The arguments
	 * @return mixed The result of the repository method
	 * @api
	 */
	public function __call($method, $arguments) {
		// TODO: Implement __call() method.
	}
}

?>
