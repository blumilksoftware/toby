<template>
  <v-chart
    style="height: 600px;"
    :option="option"
  />
</template>

<script>
import { use } from 'echarts/core'
import { CanvasRenderer } from 'echarts/renderers'
import { PieChart } from 'echarts/charts'
import {
  TitleComponent,
  TooltipComponent,
  LegendComponent,
} from 'echarts/components'
import VChart from 'vue-echarts'
import {computed} from 'vue'

use([
  CanvasRenderer,
  PieChart,
  TitleComponent,
  TooltipComponent,
  LegendComponent,
])

export default {
  name: 'VacationChart',
  components: {
    VChart,
  },
  props: {
    stats: {
      type: Object,
      default: () => ({
        used: 0,
        pending: 0,
        remaining: 0,
      }),
    },
  },
  setup(props) {
    const option = computed(() => ({
      tooltip: {
        trigger: 'item',
        formatter: '{a} <br/>{b} : {c} ({d}%)',
      },
      color: [
        '#2C466F',
        '#AABDDD',
        '#527ABA',
      ],
      legend: {
        orient: 'vertical',
        left: 'left',
        data: ['Wykorzystane', 'Rozpatrywane', 'Pozostałe'],
      },
      series: [
        {
          name: 'Urlop wypoczynkowy',
          type: 'pie',
          label: {
            show: true,
            textStyle: {
              fontSize: 16,
            },
          },
          data: [
            { value: props.stats.used, name: 'Wykorzystane' },
            { value: props.stats.pending, name: 'Rozpatrywane' },
            { value: props.stats.remaining, name: 'Pozostałe' },
          ],
          radius: ['30%', '70%'],
          itemStyle : {
            normal: {
              borderRadius: 10,
              borderColor: '#fff',
              borderWidth: 2,
              label: {
                position: 'inner',
                formatter: params => params.value,
                fontWeight: 'bold',
                fontSize: 16,
                color: '#FFFFFF',
              },
              labelLine: {
                show: false,
              },
            },
          },
        },
      ],
    }))

    return { option }
  },
}
</script>
