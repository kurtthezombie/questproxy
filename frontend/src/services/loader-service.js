import { useLoading } from 'vue-loading-overlay';

export const useLoader  = () => {
    const $loading = useLoading({
        isFullPage: true, 
        color: "#58E246", 
        height: 128, 
        width: 128, 
        loader: 'spinner', 
        backgroundColor: "#454545", 
        enforceFocus: true 
    });

    return {
        loadShow: () => {
            const loader = $loading.show();
            return loader;
        },
        loadHide: (loader) => {
            if (loader) {
                loader.hide();
            }
        }
    };
}