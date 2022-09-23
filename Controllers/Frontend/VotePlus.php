<?php

use Shopware\Models\Customer\Customer;
use WbmVotePlus\Models\VotePlus;
use Shopware\Components\Model\ModelManager;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Shopware_Controllers_Frontend_VotePlus extends Enlight_Controller_Action
{
    private ModelManager $modelManger;

    private SessionInterface $session;

    public function init(): void
    {
        $this->modelManger = $this->container->get(ModelManager::class);
        $this->session = $this->container->get('session');
    }

    /**
     * @throws Enlight_Exception
     * @throws JsonException
     */
    public function indexAction(): void
    {
        /**
         * Check whether user is loggedIn or not
         * Is not loggedIn
         * -> Response 403
         */
        $userSession = $this->session->get('userInfo');
        $userSessionId = $this->session->get('sessionId');
        if (!$userSession || !$userSessionId) {
            $this->sendResponse(false, 403);
            return;
        }

        /**
         * Get data from Post request
         */
        $postData = $this->Request()->getPost();
        $action = $postData['action'];
        $voteId = $postData['voteId'];


        /**
         * Get VotePlus repository
         */
        $voteRepository = $this->modelManger->getRepository(VotePlus::class);

        /**
         * Get user data from s_user
         */
        $customerRepo = $this->modelManger->getRepository(Customer::class);
        $customer = $customerRepo->findOneBy([
            'sessionId' => $userSessionId
        ]);

        if($action === 'up' || $action === 'down') {
            /**
             * If action is up or down
             * A new column will be added to the table
             */
            $newVote = new VotePlus();
            $newVote->setVoteId($voteId);
            $newVote->setUp($action === 'up');
            $newVote->setCustomerId($customer->getId());

            $this->modelManger->persist($newVote);
        }else {

            /**
             * Find vote by vote_id & customer_id
             */
            /** @var VotePlus $vote */
            $vote = $voteRepository->findOneBy([
                'voteId' => $voteId,
                'customerId' => $customer->getId()
            ]);

            $this->modelManger->remove($vote);
        }
        $this->modelManger->flush();
        $this->sendResponse();
    }

    public function sendResponse($success = true, $status = 200): void
    {
        /**
         * Prepare header and not render
         */
        $this->Response()->headers->set('content-type', 'application/json');
        $this->Front()->Plugins()->ViewRenderer()->setNoRender();

        /**
         * Set status Code
         * Send success true or false
         */
        $this->Response()->setStatusCode($status);
        $this->Response()->setContent(json_encode([
            'success' => $success,
            'isLoggedIn' => ($status !== 403)
        ], JSON_THROW_ON_ERROR));
    }
}