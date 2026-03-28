import { ref } from 'vue';

type SliderStyle = {
    width: string;
    height: string;
    transform: string;
    opacity: string;
};

const defaultSliderStyle: SliderStyle = {
    width: '0px',
    height: '0px',
    transform: 'translateX(0px)',
    opacity: '0',
};

const sliderStyle = ref<SliderStyle>({ ...defaultSliderStyle });
const isSliderReady = ref(false);

export function useHeaderNavPill() {
    return {
        sliderStyle,
        isSliderReady,
    };
}

export function setHeaderNavPillStyle(style: SliderStyle) {
    sliderStyle.value = { ...style };
}

export function setHeaderNavPillReady(value: boolean) {
    isSliderReady.value = value;
}

export function resetHeaderNavPill() {
    sliderStyle.value = { ...defaultSliderStyle };
    isSliderReady.value = false;
}