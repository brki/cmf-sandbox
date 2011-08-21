<?php

namespace Sandbox\MainBundle\Resources\data\fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Yaml\Parser;

use Sandbox\TestBundle\Document\TeamMember;

class LoadSTeamMemberData implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
{
    protected $session;

    protected $container;

    public function setContainer(ContainerInterface $container = null)
    {
        if (! $container) {
            throw new \Exception("This does not work without container");
        }
        $this->container = $container;
        $this->session = $this->container->get('doctrine_phpcr.default_session'); // FIXME: should get this from manager in load, not necessarily the default
    }

    public function getOrder() {
        return 40;
    }

    public function load($manager)
    {

        $repo =  'Sandbox\TestBundle\Document\TeamMember';
        $basepath = '/cms/content/teamMember';

        $this->createPath($basepath);

        $yaml = new Parser();
        $data = $yaml->parse(file_get_contents(__DIR__ . '/team/members.yml'));

        foreach($data['members'] as $member) {
            $path = $basepath . '/' . $member['name'];
            if (!$page = $manager->find($repo, $path)) {
                $page = new TeamMember();
                $page->setPath($path);
                $page->picture = new \Doctrine\ODM\PHPCR\Document\File();
                $imageSourcePath = __DIR__ . '/' . $member['picture'];
                var_dump($imageSourcePath);
                $page->picture->setFileContentFromFilesystem($imageSourcePath);
                $manager->persist($page);
                //TODO: document manager should handle this for us
                $manager->flushNoSave(); //populate node property
            }
            $page->name = $member['name'];
        }

        $manager->flush(); //to get ref id populated
    }

    /**
     * Create a node and it's parents, if necessary.  Like mkdir -p.
     *
     * TODO: clean this up once the id generator stuff is done as intended
     *
     * @param string $path  full path, like /cms/navigation/main
     * @return Node the (now for sure existing) node at path
     */
    public function createPath($path)
    {
        $current = $this->session->getRootNode();

        $segments = preg_split('#/#', $path, null, PREG_SPLIT_NO_EMPTY);
        foreach ($segments as $segment) {
            if ($current->hasNode($segment)) {
                $current = $current->getNode($segment);
            } else {
                $current = $current->addNode($segment);
            }
        }

        return $current;
    }
}
