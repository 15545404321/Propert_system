Vue.component('Add', {
	template: `
		<el-dialog title="添加" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
			<el-tabs v-model="activeName">
				<el-tab-pane style="padding-top:10px"  label="基本信息" name="基本信息">
				<el-row >
					<el-col :span="24">
						<el-form-item label="入职时间" prop="ryxx_addtime">
							<el-date-picker value-format="yyyy-MM-dd" type="date" v-model="form.ryxx_addtime" clearable placeholder="请输入入职时间"></el-date-picker>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="在职情况" prop="ryxx_zaizhi">
							<el-radio-group v-model="form.ryxx_zaizhi">
								<el-radio :label="1">在职</el-radio>
								<el-radio :label="2">离职</el-radio>
							</el-radio-group>
						</el-form-item>
					</el-col>
				</el-row>
				</el-tab-pane>
				<el-tab-pane style="padding-top:10px"  label="工资信息" name="工资信息">
				<el-row >
					<el-col :span="24">
						<el-form-item label="薪资标准" prop="ryxx_xinzi">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.ryxx_xinzi" clearable :min="0" placeholder="请输入薪资标准"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="工资结构" prop="ryxx_gzjg">
							<key-data v-if="show" :item.sync="form.ryxx_gzjg"></key-data>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="保险情况" prop="ryxx_baoxian">
							<key-data v-if="show" :item.sync="form.ryxx_baoxian"></key-data>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="开户银行" prop="ryxx_khh">
							<el-input  v-model="form.ryxx_khh" autoComplete="off" clearable  placeholder="请输入开户银行"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="银行卡号" prop="ryxx_yhkh">
							<el-input  v-model="form.ryxx_yhkh" autoComplete="off" clearable  placeholder="请输入银行卡号"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				</el-tab-pane>
			</el-tabs>
			</el-form>
			<div slot="footer" class="dialog-footer">
				<el-button :size="size" :loading="loading" type="primary" @click="submit" >
					<span v-if="!loading">确 定</span>
					<span v-else>提 交 中...</span>
				</el-button>
				<el-button :size="size" @click="closeForm">取 消</el-button>
			</div>
		</el-dialog>
	`
	,
	components:{
	},
	props: {
		show: {
			type: Boolean,
			default: false
		},
		size: {
			type: String,
			default: 'small'
		},
	},
	data(){
		return {
			form: {
				shop_id:'',
				xqgl_id:'',
				shop_admin_id:'',
				ryxx_addtime:'',
				ryxx_gzjg:[{}],
				ryxx_baoxian:[{}],
				ryxx_zaizhi:1,
				ryxx_khh:'',
				ryxx_yhkh:'',
				xqgl_id:'',
				shop_admin_id:'',
			},
			loading:false,
			activeName:'基本信息',
			rules: {
				shop_admin_id:[
				],
			}
		}
	},
	methods: {
		open(){
			const myURL = new URL(window.location.href)
			const urlobj = param2Obj(myURL.href)
			this.form.xqgl_id = urlobj.xqgl_id
			this.form.shop_admin_id = urlobj.shop_admin_id
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Ryxx/add',this.form).then(res => {
						if(res.data.status == 200){
							this.$message({message: res.data.msg, type: 'success'})
							this.$emit('refesh_list')
							this.closeForm()
						}else{
							this.loading = false
							this.$message.error(res.data.msg)
						}
					}).catch(()=>{
						this.loading = false
					})
				}
			})
		},
		closeForm(){
			this.$emit('update:show', false)
			this.loading = false
			if (this.$refs['form']!==undefined) {
				this.$refs['form'].resetFields()
			}
			this.form.ryxx_gzjg = [{}]
			this.form.ryxx_baoxian = [{}]
		},
	}
})
