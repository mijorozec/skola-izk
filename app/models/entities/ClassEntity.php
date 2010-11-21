<?php

namespace School\Entities;

/**
 * @Entity
 * @Table(name = "classes")
 */
class ClassEntity extends BaseEntity {
    private $name;

	private $teacher;
}
