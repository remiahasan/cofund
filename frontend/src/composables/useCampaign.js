import { storeToRefs } from "pinia";
import { useCampaignStore } from "@/stores/campaignStore";

export function useCampaign() {
    const store = useCampaignStore();
    const {campaigns, meta, currentCampaign, categories, isLoading} = storeToRefs(store);

    return {
        campaigns, meta, currentCampaign, categories, isLoading,
        fetchCampaigns: store.fetchCampaigns,
        fetchOne: store.fetchOne,
        fetchCategories: store.fetchCategories,
    }
}