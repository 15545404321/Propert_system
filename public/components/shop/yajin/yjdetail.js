Vue.component('Yjdetail', {
	template: `
		<el-dialog title="押金详情" width="" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">单次应收：</td>
						<td>
							{{form.zjys_dcys}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">使用数量：</td>
						<td>
							{{form.zjys_sysl}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">本次应收：</td>
						<td>
							{{form.zjys_bcys}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">开始日期：</td>
						<td>
							{{parseTime(form.zjys_ktime,'{y}-{m}-{d}')}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">结束时间：</td>
						<td>
							{{parseTime(form.zjys_jtime,'{y}-{m}-{d}')}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">押金状态：</td>
						<td>
							<span v-if="form.tui_status == '1'">已收取</span>
							<span v-if="form.tui_status == '2'">已退回</span>
							<span v-if="form.tui_status == '3'">转预存</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">操作时间：</td>
						<td>
							{{parseTime(form.tui_time)}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">押金备注：</td>
						<td>
							{{form.tui_beizhu}}
						</td>
					</tr>
				</tbody>
			</table>
		</el-dialog>
	`
	,
	props: {
		show: {
			type: Boolean,
			default: true
		},
		size: {
			type: String,
			default: 'mini'
		},
		info: {
			type: Object,
		},
	},
	data() {
		return {
			form:{
			},
		}
	},
	methods: {
		open(){
			axios.post(base_url+'/Yajin/yjdetail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
