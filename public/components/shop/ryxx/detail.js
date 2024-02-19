Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">开资项目：</td>
						<td>
							{{form.xqgl_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">员工姓名：</td>
						<td>
							{{form.cname}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">入职时间：</td>
						<td>
							{{parseTime(form.ryxx_addtime,'{y}-{m}-{d}')}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">薪资标准：</td>
						<td>
							{{form.ryxx_xinzi}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">工资结构：</td>
						<td>
							{{form.ryxx_gzjg}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">保险情况：</td>
						<td>
							{{form.ryxx_baoxian}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">在职情况：</td>
						<td>
							<span v-if="form.ryxx_zaizhi == '1'">在职</span>
							<span v-if="form.ryxx_zaizhi == '2'">离职</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">开户银行：</td>
						<td>
							{{form.ryxx_khh}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">银行卡号：</td>
						<td>
							{{form.ryxx_yhkh}}
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
			axios.post(base_url+'/Ryxx/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
