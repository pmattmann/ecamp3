<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'strategy', type: 'string')]
abstract class ContentNodeParent extends ContentNode {
    /**
     * All content nodes that are direct children of this content node.
     */
    #[ApiProperty(writable: false, example: '["/content_nodes/1a2b3c4d"]')]
    #[Groups(['read'])]
    #[ORM\OneToMany(targetEntity: ContentNode::class, mappedBy: 'parent', cascade: ['persist'])]
    public Collection $children;

    public function __construct() {
        parent::__construct();
        $this->children = new ArrayCollection();
    }

    /**
     * @return ContentNode[]
     */
    public function getChildren(): array {
        return $this->children->getValues();
    }

    public function addChild(ContentNode $child): self {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->parent = $this;
        }

        return $this;
    }

    public function removeChild(ContentNode $child): self {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->parent === $this) {
                $child->parent = null;
            }
        }

        return $this;
    }

    public function copyFromPrototype($prototype, $entityMap): void {
        parent::copyFromPrototype($prototype, $entityMap);

        /** @var ContentNodeParent $contentNodeParentPrototype */
        $contentNodeParentPrototype = $prototype;

        // deep copy children
        foreach ($contentNodeParentPrototype->getChildren() as $childPrototype) {
            $childClass = $this->getObjectClass($childPrototype);

            /** @var ContentNode $childContentNode */
            $childContentNode = new $childClass();

            $this->addChild($childContentNode);
            $this->root->addRootDescendant($childContentNode);

            $childContentNode->copyFromPrototype($childPrototype, $entityMap);
        }
    }
}
