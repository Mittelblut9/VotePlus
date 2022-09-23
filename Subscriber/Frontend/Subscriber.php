<?php
declare(strict_types=1);

namespace WbmVotePlus\Subscriber\Frontend;

use Enlight\Event\SubscriberInterface;
use Shopware\Components\Model\ModelManager;
use Shopware\Models\Customer\Customer;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use WbmVotePlus\Models\VotePlus;
use Enlight_Controller_ActionEventArgs;

class Subscriber implements SubscriberInterface
{
    private ModelManager $modelManger;

    private SessionInterface $session;

    public function __construct(ModelManager $modelManager, SessionInterface $session)
    {
        $this->modelManger = $modelManager;
        $this->session = $session;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'Enlight_Controller_Action_PostDispatchSecure_Frontend_Detail' => 'onFrontendListing',
        ];
    }

    public function onFrontendListing(Enlight_Controller_ActionEventArgs $args): void
    {
        /** @var \Shopware_Controllers_Frontend_Detail $subject */
        $subject = $args->getSubject();

        $data = $subject->View()->getAssign();
        $voteComments = $data['sArticle']['sVoteComments'];
        $repository = $this->modelManger->getRepository(VotePlus::class);

        $upvotes = 0;
        $downvotes = 0;


        foreach ($voteComments as $key => $voteComment) {

            $votes = $repository->findBy([
                'voteId' => $voteComment['id']
            ]);

            if(!$votes) {
                $voteComments[$key]['count'] = 0;
                continue;
            }
            foreach ($votes as $vote) {
                $voteComments[$key]['count'] = ($vote->isUp()) ? $voteComments[$key]['count'] + 1 : $voteComments[$key]['count'] - 1;
            }

            $userSessionId = $this->session->get('sessionId');
            if(!$userSessionId) {
                continue;
            }

            $customerRepo = $this->modelManger->getRepository(Customer::class);

            $customer = $customerRepo->findOneBy([
                'sessionId' => $userSessionId
            ]);

            foreach ($votes as $vote) {
                if($vote->getCustomerId() === $customer->getId()) {
                    $voteComments[$key]['hasUpVoted'] = $vote->isUp();
                    $voteComments[$key]['hasDownVoted'] = !$vote->isUp();
                    continue;
                }
            }
        }
        $data['sArticle']['sVoteComments'] = $voteComments;
        $subject->View()->assign($data);
    }
}
