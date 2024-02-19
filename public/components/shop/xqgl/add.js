Vue.component('Add', {
	template: `
		<el-dialog title="新增项目" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="项目名称" prop="xqgl_name">
							<el-input  v-model="form.xqgl_name" autoComplete="off" clearable  placeholder="请输入项目名称"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="管理面积" prop="xqgl_glmj">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.xqgl_glmj" clearable :min="0" placeholder="请输入管理面积"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="房间数量" prop="xqgl_fjsl">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.xqgl_fjsl" clearable :min="0" placeholder="请输入房间数量"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="车位数量" prop="xqgl_cwsl">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.xqgl_cwsl" clearable :min="0" placeholder="请输入车位数量"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="车辆数量" prop="xqgl_clsl">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.xqgl_clsl" clearable :min="0" placeholder="请输入车辆数量"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="月应收费" prop="xqgl_yysf">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.xqgl_yysf" clearable :min="0" placeholder="请输入月应收费"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="年应收费" prop="xqgl_nysf">
							<el-input-number controls-position="right" style="width:200px;" autoComplete="off" v-model="form.xqgl_nysf" clearable :min="0" placeholder="请输入年应收费"/>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="省份市区" prop="xqgl_address">
							<shengshiqu v-if="show" :checkstrictly="{ checkStrictly: false }" :type="1" :treeoption.sync="form.xqgl_address"></shengshiqu>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="22">
						<el-form-item label="经度纬度" class="map" prop="xggl_jdwd">
							<el-input type="textarea"  v-model="form.xggl_jdwd" placeholder="请选择坐标位置" @click.native="xggl_jdwdDialogStatus = true" readonly clearable></el-input>
						</el-form-item>
					</el-col>
					<el-col :span="1">
						<div class="el-input-group__append" @click="xggl_jdwdDialogStatus = true" style="height:50px;background-color:#fff;padding:0 13px;cursor:pointer">
							<i style="font-size:20px" class="el-icon-location"></i>
						</div>
					</el-col>
				</el-row>
			</el-form>
			<div slot="footer" class="dialog-footer">
				<el-button :size="size" :loading="loading" type="primary" @click="submit" >
					<span v-if="!loading">确 定</span>
					<span v-else>提 交 中...</span>
				</el-button>
				<el-button :size="size" @click="closeForm">取 消</el-button>
			</div>
			<tx-map v-if="xggl_jdwdDialogStatus" :show.sync="xggl_jdwdDialogStatus" :address_detail.sync="form.xggl_jdwd"></tx-map>
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
				xqgl_name:'',
				xqgl_address:[],
				xggl_jdwd:'',
			},
			xggl_jdwdDialogStatus:false,
			loading:false,
			rules: {
				xqgl_name:[
					{required: true, message: '项目名称不能为空', trigger: 'blur'},
				],
			}
		}
	},
	methods: {
		open(){
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Xqgl/add',this.form).then(res => {
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
		},
	}
})
