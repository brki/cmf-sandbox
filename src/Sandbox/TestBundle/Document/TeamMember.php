<?php

namespace Sandbox\TestBundle\Document;

use Doctrine\ODM\PHPCR\Mapping\Annotations as PHPCRODM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @PHPCRODM\Document(alias="teamMemberTest")
 */
class TeamMember
{

    /** @PHPCRODM\Id */
    protected $path;

    /** @PHPCRODM\Node */
    protected $node;

    /**
     * @Assert\NotBlank
     * @PHPCRODM\String()
     */
    public $name;

    /**
     * @PHPCRODM\Child
     */
    public $picture;

    public function setPath($path)
    {
      $this->path = $path;
    }

    public function getPath()
    {
      return $this->path;
    }

    public function getNode()
    {
      return $this->node;
    }


}
